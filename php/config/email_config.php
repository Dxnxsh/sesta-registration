<?php
/**
 * Email Configuration
 * Centralized email settings for the SESTA Registration System
 */

// Include Composer autoloader for PHPMailer
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Configuration Class
 */
class EmailConfig
{
    // Default email settings
    const DEFAULT_FROM_EMAIL = 'support@sesta.com';
    const DEFAULT_FROM_NAME = 'SEKOLAH MENENGAH SAINS TAPAH';
    const DEFAULT_CHARSET = 'UTF-8';
    
    // SMTP Settings (configure these according to your email provider)
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_SECURE = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS
    const SMTP_AUTH = true;
    const SMTP_USERNAME = 'asyraffdanial6@gmail.com'; // Gmail username from old config
    const SMTP_PASSWORD = 'wkbprjeutsgjyols'; // Gmail app password from old config
    
    /**
     * Create a new configured PHPMailer instance
     * 
     * @param bool $exceptions Enable exceptions
     * @return PHPMailer
     */
    public static function createMailer($exceptions = true)
    {
        $mail = new PHPMailer($exceptions);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = self::SMTP_HOST;
        $mail->SMTPAuth = self::SMTP_AUTH;
        $mail->Username = self::SMTP_USERNAME;
        $mail->Password = self::SMTP_PASSWORD;
        $mail->SMTPSecure = self::SMTP_SECURE;
        $mail->Port = self::SMTP_PORT;
        
        // Default settings
        $mail->CharSet = self::DEFAULT_CHARSET;
        $mail->setFrom(self::DEFAULT_FROM_EMAIL, self::DEFAULT_FROM_NAME);
        $mail->isHTML(true);
        
        // Debug settings (disable in production)
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        
        return $mail;
    }
    
    /**
     * Create a simple mail instance using PHP's mail() function
     * Useful for basic emails when SMTP is not required
     * 
     * @param bool $exceptions Enable exceptions
     * @return PHPMailer
     */
    public static function createSimpleMailer($exceptions = true)
    {
        $mail = new PHPMailer($exceptions);
        
        // Use PHP's mail() function
        $mail->isMail();
        
        // Default settings
        $mail->CharSet = self::DEFAULT_CHARSET;
        $mail->setFrom(self::DEFAULT_FROM_EMAIL, self::DEFAULT_FROM_NAME);
        $mail->isHTML(true);
        
        return $mail;
    }
    
    /**
     * Send a password recovery email
     * 
     * @param string $email Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @return array Result array with success status and message
     */
    public static function sendPasswordRecoveryEmail($email, $subject, $body)
    {
        try {
            $mail = self::createMailer(); // Use SMTP mailer instead of simple mailer
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            if ($mail->send()) {
                return [
                    'success' => true,
                    'message' => 'Email sent successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Email could not be sent: ' . $mail->ErrorInfo
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email could not be sent: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Send an email with attachment (like invoice/receipt)
     * 
     * @param string $email Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @param string $attachmentPath Path to attachment file
     * @param string $attachmentName Name for the attachment
     * @return array Result array with success status and message
     */
    public static function sendEmailWithAttachment($email, $subject, $body, $attachmentPath = null, $attachmentName = null)
    {
        try {
            $mail = self::createMailer(); // Use SMTP mailer instead of simple mailer
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Add attachment if provided
            if ($attachmentPath && file_exists($attachmentPath)) {
                $mail->addAttachment($attachmentPath, $attachmentName ?: basename($attachmentPath));
            }
            
            if ($mail->send()) {
                return [
                    'success' => true,
                    'message' => 'Email with attachment sent successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Email could not be sent: ' . $mail->ErrorInfo
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email could not be sent: ' . $e->getMessage()
            ];
        }
    }
}
