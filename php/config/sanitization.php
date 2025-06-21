<?php
/**
 * Sanitization Configuration and Functions
 * Provides comprehensive input sanitization and validation for the SESTA Registration System
 * Based on actual database schema patterns
 */

class SecuritySanitizer {
    
    private static $connection = null;
    
    // Database field patterns based on actual schema
    private static $patterns = [
        // ID patterns (based on actual database constraints)
        'admin_id' => '/^[A-Z0-9]{1,6}$/',
        'student_id' => '/^[A-Z0-9]{1,12}$/',
        'teacher_id' => '/^[A-Z0-9]{1,12}$/',
        'parent_id' => '/^[A-Z0-9]{1,12}$/',
        'class_code' => '/^[A-Z0-9]{1,11}$/',
        'payment_id' => '/^\d{1,10}$/',
        
        // Names and text fields
        'name' => '/^[a-zA-Z\s\'\-\.]{1,200}$/',
        'username' => '/^[a-zA-Z0-9_]{3,50}$/',
        'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        
        // Phone numbers (Malaysian format - 10-11 digits)
        'phone' => '/^[0-9]{10,11}$/',
        
        // Passwords (max 15 characters based on DB constraint, no minimum)
        'password' => '/^.{1,15}$/',
        
        // Text fields
        'address' => '/^[a-zA-Z0-9\s\,\.\-\/\(\)]{1,200}$/',
        'job' => '/^[a-zA-Z\s\-]{1,50}$/',
        'class_name' => '/^[a-zA-Z0-9\s\-]{1,50}$/',
        'class_level' => '/^[a-zA-Z0-9]{1,10}$/',
        'class_block' => '/^[a-zA-Z0-9\s\-]{1,50}$/',
        'class_category' => '/^[a-zA-Z\s\-]{1,50}$/',
        
        // Enums and specific values (based on database patterns)
        'gender' => '/^(Male|Female)$/',
        'religion' => '/^[a-zA-Z\s]{1,50}$/',
        'race' => '/^[a-zA-Z\s]{1,50}$/',
        'nationality' => '/^[a-zA-Z\s]{1,50}$/',
        'status' => '/^[a-zA-Z\s]{1,20}$/',
        'payment_type' => '/^[a-zA-Z\s\-]{1,50}$/',
        'payment_status' => '/^(Pending|Completed|Failed|Refunded)$/',
        
        // Numeric fields
        'floor' => '/^\d{1,2}$/',
        'amount' => '/^\d{1,4}(\.\d{1,2})?$/',
        'income' => '/^\d{1,4}(\.\d{1,2})?$/',
        
        // Date fields
        'date' => '/^\d{4}-\d{2}-\d{2}$/',
        'datetime' => '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',
        
        // File paths (for uploads - up to 1000 chars based on PAYMENT_RECEIPT)
        'file_path' => '/^[a-zA-Z0-9\/_\-\.]{1,1000}$/',
        
        // Password reset key
        'reset_key' => '/^[a-zA-Z0-9]{1,250}$/',
        
        // Action types (for PDF actions, form actions, etc.)
        'action' => '/^[A-Z_]{1,20}$/',
    ];
    
    // Maximum lengths for database fields (from actual schema)
    private static $maxLengths = [
        'ADMIN_ID' => 6,
        'ADMIN_NAME' => 200,
        'ADMIN_PHONE' => 11,
        'ADMIN_PWD' => 15,
        'ADMIN_USERNAME' => 20,
        'STUDENT_ID' => 12,
        'STUDENT_PWD' => 15,
        'STUDENT_NAME' => 50,
        'STUDENT_GENDER' => 6,
        'STUDENT_LEVEL' => 6,
        'STUDENT_POB' => 50,
        'STUDENT_RELIGION' => 50,
        'STUDENT_RACE' => 50,
        'STUDENT_NATIONALITY' => 50,
        'STUDENT_ADDRESS' => 100,
        'STUDENT_DISEASE' => 50,
        'STUDENT_DISABILITY' => 50,
        'STUDENT_STATUS' => 20,
        'STUDENT_EMAIL' => 200,
        'TEACHER_ID' => 12,
        'TEACHER_USERNAME' => 50,
        'TEACHER_PWD' => 15,
        'TEACHER_NAME' => 50,
        'TEACHER_EMAIL' => 50,
        'TEACHER_GENDER' => 6,
        'TEACHER_ADDRESS' => 200,
        'TEACHER_PHONENUM' => 11,
        'TEACHER_STATUS' => 20,
        'PARENT_ID' => 12,
        'PARENT_NAME' => 50,
        'PARENT_PHONENUM' => 11,
        'PARENT_JOB' => 50,
        'PARENT_GENDER' => 6,
        'PARENT_MONTHLY_INCOME' => 6, // decimal(6,2)
        'CLASS_CODE' => 11,
        'CLASS_NAME' => 50,
        'CLASS_LEVEL' => 10,
        'CLASS_BLOCK' => 50,
        'CLASS_CAT' => 50,
        'PAYMENT_TYPE' => 50,
        'PAYMENT_STATUS' => 50,
        'PAYMENT_RECEIPT' => 1000,
        'PAYMENT_AMOUNT' => 6, // decimal(6,2)
        'RESET_EMAIL' => 250,
        'RESET_KEY' => 250,
    ];
    
    // Valid enum values based on common patterns
    private static $enums = [
        'gender' => ['Male', 'Female'],
        'payment_status' => ['Pending', 'Completed', 'Failed', 'Refunded'],
        'student_status' => ['Active', 'Inactive', 'Graduated', 'Transferred'],
        'teacher_status' => ['Active', 'Inactive', 'Retired'],
        'class_level' => ['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6'],
    ];
    
    /**
     * Initialize with database connection
     */
    public static function init($connection) {
        self::$connection = $connection;
    }
    
    /**
     * Sanitize input based on type and database constraints
     */
    public static function sanitize($input, $type, $fieldName = null) {
        if ($input === null || $input === '') {
            return $input;
        }
        
        // Basic sanitization
        $input = trim($input);
        $input = stripslashes($input);
        
        // Type-specific sanitization
        switch ($type) {
            case 'id':
                return self::sanitizeId($input, $fieldName);
            case 'name':
                return self::sanitizeName($input);
            case 'email':
                return self::sanitizeEmail($input);
            case 'phone':
                return self::sanitizePhone($input);
            case 'password':
                return self::sanitizePassword($input);
            case 'text':
                return self::sanitizeText($input, $fieldName);
            case 'number':
                return self::sanitizeNumber($input);
            case 'decimal':
                return self::sanitizeDecimal($input);
            case 'date':
                return self::sanitizeDate($input);
            case 'datetime':
                return self::sanitizeDateTime($input);
            case 'enum':
                return self::sanitizeEnum($input, $fieldName);
            case 'address':
                return self::sanitizeAddress($input);
            case 'gender':
                return self::sanitizeGender($input);
            case 'file_path':
                return self::sanitizeFilePath($input);
            case 'longtext':
                return self::sanitizeLongText($input);
            case 'class_name':
                return self::sanitizeClassName($input);
            case 'class_level':
                return self::sanitizeClassLevel($input);
            case 'class_block':
                return self::sanitizeClassBlock($input);
            case 'class_category':
                return self::sanitizeClassCategory($input);
            case 'class_code':
                return self::sanitizeClassCode($input);
            case 'floor':
                return self::sanitizeFloor($input);
            case 'religion':
                return self::sanitizeReligion($input);
            case 'race':
                return self::sanitizeRace($input);
            case 'nationality':
                return self::sanitizeNationality($input);
            case 'status':
                return self::sanitizeStatus($input);
            case 'job':
                return self::sanitizeJob($input);
            case 'username':
                return self::sanitizeUsername($input);
            case 'action':
                return self::sanitizeAction($input);
            default:
                return self::sanitizeGeneral($input, $fieldName);
        }
    }
    
    /**
     * Validate input against database constraints
     */
    public static function validate($input, $type, $fieldName = null) {
        if ($input === null || $input === '') {
            return true; // Assuming nullable fields, check constraints separately
        }
        
        // Check maximum length
        if ($fieldName && isset(self::$maxLengths[$fieldName])) {
            if (strlen($input) > self::$maxLengths[$fieldName]) {
                return false;
            }
        }
        
        // Pattern validation
        $pattern = self::getPattern($type, $fieldName);
        if ($pattern && !preg_match($pattern, $input)) {
            return false;
        }
        
        // Enum validation
        if ($type === 'enum' && $fieldName) {
            $enumKey = strtolower(str_replace(['_ID', '_NAME'], '', $fieldName));
            if (isset(self::$enums[$enumKey]) && !in_array($input, self::$enums[$enumKey])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Sanitize and validate combined
     */
    public static function sanitizeAndValidate($input, $type, $fieldName = null) {
        $sanitized = self::sanitize($input, $type, $fieldName);
        
        if (!self::validate($sanitized, $type, $fieldName)) {
            self::logSecurityEvent('validation_failed', [
                'field' => $fieldName,
                'type' => $type,
                'input' => substr($input, 0, 100), // Log first 100 chars only
                'sanitized' => substr($sanitized, 0, 100)
            ]);
            throw new InvalidArgumentException("Invalid input for field: " . ($fieldName ?: $type));
        }
        
        return $sanitized;
    }
    
    /**
     * Escape for database insertion
     */
    public static function escapeForDB($input) {
        if (self::$connection && $input !== null) {
            return mysqli_real_escape_string(self::$connection, $input);
        }
        return $input;
    }
    
    /**
     * Complete sanitization for database insertion
     */
    public static function sanitizeForDB($input, $type, $fieldName = null) {
        $sanitized = self::sanitizeAndValidate($input, $type, $fieldName);
        return self::escapeForDB($sanitized);
    }
    
    // Private helper methods
    
    private static function sanitizeId($input, $fieldName) {
        // Remove any non-alphanumeric characters
        $sanitized = preg_replace('/[^A-Z0-9]/', '', strtoupper($input));
        
        // Truncate to appropriate length
        if ($fieldName && isset(self::$maxLengths[$fieldName])) {
            $sanitized = substr($sanitized, 0, self::$maxLengths[$fieldName]);
        }
        
        return $sanitized;
    }
    
    private static function sanitizeName($input) {
        // Allow letters, spaces, apostrophes, hyphens, and dots
        $sanitized = preg_replace('/[^a-zA-Z\s\'\-\.]/', '', $input);
        $sanitized = preg_replace('/\s+/', ' ', $sanitized); // Normalize spaces
        return trim($sanitized);
    }
    
    private static function sanitizeEmail($input) {
        // Remove all whitespace first
        $input = preg_replace('/\s+/', '', $input);
        $sanitized = filter_var($input, FILTER_SANITIZE_EMAIL);
        return filter_var($sanitized, FILTER_VALIDATE_EMAIL) ? $sanitized : null;
    }
    
    private static function sanitizePhone($input) {
        // Remove all non-numeric characters
        $sanitized = preg_replace('/[^0-9]/', '', $input);
        
        // Handle Malaysian phone format
        if (strlen($sanitized) >= 10 && strlen($sanitized) <= 11) {
            // Ensure it starts with 0 for local format
            if (strlen($sanitized) == 10 && $sanitized[0] != '0') {
                $sanitized = '0' . $sanitized;
            }
        }
        
        return $sanitized;
    }
    
    private static function sanitizePassword($input) {
        // Don't modify password content, just validate length
        if (strlen($input) >= 6 && strlen($input) <= 15) {
            return $input;
        }
        return null;
    }
    
    private static function sanitizeText($input, $fieldName) {
        // Allow alphanumeric, spaces, and common punctuation
        $sanitized = preg_replace('/[^a-zA-Z0-9\s\,\.\-\/\(\)\'\:]/u', '', $input);
        $sanitized = preg_replace('/\s+/', ' ', $sanitized); // Normalize spaces
        
        // Truncate to field length
        if ($fieldName && isset(self::$maxLengths[$fieldName])) {
            $sanitized = substr($sanitized, 0, self::$maxLengths[$fieldName]);
        }
        
        return trim($sanitized);
    }
    
    private static function sanitizeNumber($input) {
        $sanitized = filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        return is_numeric($sanitized) ? intval($sanitized) : null;
    }
    
    private static function sanitizeDecimal($input) {
        $sanitized = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return is_numeric($sanitized) ? floatval($sanitized) : null;
    }
    
    private static function sanitizeDate($input) {
        // Validate and format date
        $date = DateTime::createFromFormat('Y-m-d', $input);
        if ($date && $date->format('Y-m-d') === $input) {
            return $input;
        }
        
        // Try other common formats
        $formats = ['d/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $input);
            if ($date) {
                return $date->format('Y-m-d');
            }
        }
        
        return null;
    }
    
    private static function sanitizeDateTime($input) {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $input);
        if ($datetime && $datetime->format('Y-m-d H:i:s') === $input) {
            return $input;
        }
        
        // Try to parse and reformat
        $datetime = DateTime::createFromFormat('Y-m-d H:i', $input);
        if ($datetime) {
            return $datetime->format('Y-m-d H:i:s');
        }
        
        return null;
    }
    
    private static function sanitizeEnum($input, $fieldName) {
        $enumKey = strtolower(str_replace(['_ID', '_NAME'], '', $fieldName ?: ''));
        
        if (isset(self::$enums[$enumKey])) {
            return in_array($input, self::$enums[$enumKey]) ? $input : null;
        }
        
        return $input;
    }
    
    private static function sanitizeFilePath($input) {
        // Remove dangerous characters and normalize path
        $sanitized = preg_replace('/[^a-zA-Z0-9\/_\-\.]/', '', $input);
        $sanitized = preg_replace('/\.\.+/', '', $sanitized); // Remove directory traversal
        $sanitized = preg_replace('/\/+/', '/', $sanitized); // Normalize slashes
        $sanitized = ltrim($sanitized, '/'); // Remove leading slash from traversal
        return $sanitized;
    }
    
    private static function sanitizeLongText($input) {
        // For longtext fields like STUDENT_FACE, TEACHER_FACE (base64 encoded data)
        // Allow base64 characters and basic formatting
        return preg_replace('/[^a-zA-Z0-9+\/=\s]/', '', $input);
    }
    
    private static function sanitizeAddress($input) {
        // For address fields - allow letters, numbers, spaces, and common punctuation
        $sanitized = preg_replace('/[^a-zA-Z0-9\s\,\.\-\/\(\)]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeGender($input) {
        // For gender fields - strict validation
        $input = trim($input);
        return in_array($input, ['Male', 'Female']) ? $input : '';
    }
    
    private static function sanitizeClassName($input) {
        // For class names - allow letters, numbers, spaces, and hyphens
        $sanitized = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeClassLevel($input) {
        // For class levels - Form 1, Form 2, etc.
        $sanitized = preg_replace('/[^a-zA-Z0-9\s]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeClassBlock($input) {
        // For class blocks - allow letters, numbers, spaces, and hyphens
        $sanitized = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeClassCategory($input) {
        // For class categories - allow letters, spaces, and hyphens
        $sanitized = preg_replace('/[^a-zA-Z\s\-]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeClassCode($input) {
        // For class codes - alphanumeric only
        $sanitized = preg_replace('/[^A-Z0-9]/', '', strtoupper($input));
        return $sanitized;
    }
    
    private static function sanitizeFloor($input) {
        // For floor numbers - numeric only, 1-2 digits
        $sanitized = preg_replace('/[^0-9]/', '', $input);
        return $sanitized ? intval($sanitized) : null;
    }
    
    private static function sanitizeReligion($input) {
        // For religion fields - allow letters and spaces
        $sanitized = preg_replace('/[^a-zA-Z\s]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeRace($input) {
        // For race fields - allow letters and spaces
        $sanitized = preg_replace('/[^a-zA-Z\s]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeNationality($input) {
        // For nationality fields - allow letters and spaces
        $sanitized = preg_replace('/[^a-zA-Z\s]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeStatus($input) {
        // For status fields - allow letters and spaces
        $sanitized = preg_replace('/[^a-zA-Z\s]/', '', $input);
        return trim($sanitized);
    }
    
    private static function sanitizeJob($input) {
        // For job fields - allow letters, spaces, and hyphens
        $sanitized = preg_replace('/[^a-zA-Z\s\-]/', '', $input);
        return trim($sanitized);
    }

    private static function sanitizeUsername($input) {
        // For username fields - allow letters, numbers, and underscores only
        $sanitized = preg_replace('/[^a-zA-Z0-9_]/', '', $input);
        return trim($sanitized);
    }

    private static function sanitizeAction($input) {
        // For action fields - allow uppercase letters and underscores only (VIEW, DOWNLOAD, etc.)
        $sanitized = preg_replace('/[^A-Z_]/', '', strtoupper($input));
        return trim($sanitized);
    }

    private static function sanitizeGeneral($input, $fieldName) {
        $sanitized = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        // Truncate to field length if specified
        if ($fieldName && isset(self::$maxLengths[$fieldName])) {
            $sanitized = substr($sanitized, 0, self::$maxLengths[$fieldName]);
        }
        
        return $sanitized;
    }
    
    private static function getPattern($type, $fieldName) {
        // First check for field-specific pattern
        if ($fieldName) {
            $lowerFieldName = strtolower($fieldName);
            
            // Direct field name match
            if (isset(self::$patterns[$lowerFieldName])) {
                return self::$patterns[$lowerFieldName];
            }
            
            // Pattern matching for field names
            foreach (self::$patterns as $key => $pattern) {
                if (strpos($lowerFieldName, $key) !== false) {
                    return $pattern;
                }
            }
        }
        
        // Then check for type pattern
        if (isset(self::$patterns[$type])) {
            return self::$patterns[$type];
        }
        
        return null;
    }
    
    /**
     * Sanitize array of inputs
     */
    public static function sanitizeArray($inputs, $types, $fieldNames = []) {
        $sanitized = [];
        
        foreach ($inputs as $key => $value) {
            $type = isset($types[$key]) ? $types[$key] : 'text';
            $fieldName = isset($fieldNames[$key]) ? $fieldNames[$key] : $key;
            
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value, $types, $fieldNames);
            } else {
                try {
                    $sanitized[$key] = self::sanitizeForDB($value, $type, $fieldName);
                } catch (InvalidArgumentException $e) {
                    // Log the error and set to null or default value
                    self::logSecurityEvent('sanitization_failed', [
                        'field' => $fieldName,
                        'error' => $e->getMessage()
                    ]);
                    $sanitized[$key] = null;
                }
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($event, $details = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'session_id' => session_id() ?? 'none',
            'details' => $details
        ];
        
        // Log to file (ensure logs directory exists and is writable)
        $logFile = dirname(__DIR__) . '/logs/security.log';
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Check for SQL injection patterns
     */
    public static function detectSQLInjection($input) {
        $patterns = [
            '/(\bunion\b|\bselect\b|\binsert\b|\bupdate\b|\bdelete\b|\bdrop\b|\bcreate\b|\balter\b)/i',
            '/(\bor\b|\band\b)\s+[\w\s]*=[\w\s]*/i',
            '/[\'";]/',
            '/\/\*.*?\*\//',
            '/--.*/',
            '/\bexec\b|\bexecute\b/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check for XSS patterns
     */
    public static function detectXSS($input) {
        $patterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe\b[^>]*>/i',
            '/<embed\b[^>]*>/i',
            '/<object\b[^>]*>/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
}

// Convenience functions for quick access
function sanitize($input, $type, $fieldName = null) {
    return SecuritySanitizer::sanitize($input, $type, $fieldName);
}

function sanitizeForDB($input, $type, $fieldName = null) {
    return SecuritySanitizer::sanitizeForDB($input, $type, $fieldName);
}

function validateInput($input, $type, $fieldName = null) {
    return SecuritySanitizer::validate($input, $type, $fieldName);
}

function detectMaliciousInput($input) {
    $sqlDetected = SecuritySanitizer::detectSQLInjection($input);
    $xssDetected = SecuritySanitizer::detectXSS($input);
    
    if ($sqlDetected && $xssDetected) {
        SecuritySanitizer::logSecurityEvent('mixed_attack_attempt', ['input' => substr($input, 0, 200)]);
        return 'mixed_attack';
    } elseif ($xssDetected) {
        SecuritySanitizer::logSecurityEvent('xss_attempt', ['input' => substr($input, 0, 200)]);
        return 'xss';
    } elseif ($sqlDetected) {
        SecuritySanitizer::logSecurityEvent('sql_injection_attempt', ['input' => substr($input, 0, 200)]);
        return 'sql_injection';
    }
    
    return false;
}

?>
