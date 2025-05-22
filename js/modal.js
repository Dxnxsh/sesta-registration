// Modal functionality
const modal = document.getElementById('faceModal');
const openModalBtn = document.getElementById('openModalBtn');
const dropArea = document.getElementById('dropArea');
const fileInput = document.getElementById('faceFile');
const filePreview = document.getElementById('filePreview');
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const capturedImage = document.getElementById('capturedImage');
activeForm = null;

// Open modal
function openModal(formId) {
    activeForm = formId;
    modal.classList.add('show');
    setTimeout(() => {
        document.querySelector('.modal-container').style.opacity = 1;
        document.querySelector('.modal-container').style.transform = 'translateY(0)';
    }, 10);

    if (document.getElementById('webcamTab').classList.contains('active')) {
        initWebcam();
    }
}

// Close modal
function closeModal() {
    document.querySelector('.modal-container').style.opacity = 0;
    document.querySelector('.modal-container').style.transform = 'translateY(20px)';

    setTimeout(() => {
        modal.classList.remove('show');
        if (video.srcObject) {
            const stream = video.srcObject;
            const tracks = stream.getTracks();
            tracks.forEach(track => track.stop());
            video.srcObject = null;
        }
    }, 300);
}

// Tab functionality
function openTab(tabName) {
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });

    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    document.getElementById(tabName).classList.add('active');
    document.querySelector(`[onclick="openTab('${tabName}')"]`).classList.add('active');

    if (tabName === 'webcamTab' && modal.classList.contains('show')) {
        initWebcam();
    } else if (tabName === 'uploadTab' && video.srcObject) {
        const stream = video.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        video.srcObject = null;
    }
}

// Initialize webcam
function initWebcam() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                video.srcObject = stream;
                video.play();
            })
            .catch(function (error) {
                console.error("Unable to access the camera/webcam: ", error);
                alert("Unable to access the camera/webcam. Please make sure you have granted permission.");
            });
    } else {
        alert("Your browser doesn't support webcam access. Please try another browser or use the upload option.");
    }
}

function captureFace() {
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    canvas.toBlob(blob => {
        const file = new File([blob], "capture.jpg", { type: "image/jpeg" });
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById("faceFile").files = dt.files;
    }, "image/jpeg");
    capturedImage.innerHTML = `<img src="${canvas.toDataURL("image/jpeg")}" alt="Captured face">`;
    capturedImage.style.display = 'block';
}
function registerFace(id) {
    console.log(activeForm);
    const form = document.getElementById(activeForm);
    console.log(document.getElementById(activeForm));
    const formData = new FormData(form);
    const role = formData.get("role");
    const username = id;
    console.log(username);
    console.log(role);

    const fileInput = document.getElementById("faceFile");
    const file = fileInput.files[0];

    if (!file) {
        alert("Please select a face image.");
        return;
    }

    const reader = new FileReader();
    reader.onload = function () {
        const base64 = reader.result.split(',')[1];
        fetch("faceRegister.php", {
            method: "POST",
            body: JSON.stringify({
                username,
                role,
                face_image: base64
            }),
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    //window.location.href = '../login-logout/login.php';
                    console.log("Face verification successful!");
                } else {
                    alert("Face verification failed!");
                    openModal(activeForm);
                }
            });
    };
    reader.readAsDataURL(file);
}

function submitFace() {
    console.log(activeForm);
    const form = document.getElementById(activeForm);
    console.log(document.getElementById(activeForm));
    const formData = new FormData(form);
    const role = formData.get("role");
    const username = formData.get("username");

    const fileInput = document.getElementById("faceFile");
    const file = fileInput.files[0];

    if (!file) {
        alert("Please select a face image.");
        return;
    }

    const reader = new FileReader();
    reader.onload = function () {
        const base64 = reader.result.split(',')[1];
        fetch("faceVerify.php", {
            method: "POST",
            body: JSON.stringify({
                username,
                role,
                face_image: base64
            }),
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Continue to loginVerify.php
                    const actualForm = document.createElement("form");
                    for (let [key, value] of formData.entries()) {
                        const input = document.createElement("input");
                        input.type = "hidden";
                        input.name = key;
                        input.value = value;
                        actualForm.appendChild(input);
                    }
                    actualForm.action = "loginVerify.php";
                    actualForm.method = "POST";
                    document.body.appendChild(actualForm);
                    actualForm.submit();
                } else {
                    alert("Face verification failed!");
                    openModal(activeForm);
                }
            });
    };
    reader.readAsDataURL(file);
}

// File upload preview
fileInput.addEventListener('change', function () {
    if (this.files && this.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
            filePreview.innerHTML = `<img src="${e.target.result}" alt="Selected face">`;
            filePreview.style.display = 'block';
            document.querySelector('.file-upload-prompt').style.display = 'none';
        };

        reader.readAsDataURL(this.files[0]);
    }
});

// Drag and drop functionality
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

function highlight() {
    dropArea.classList.add('drag-over');
}

function unhighlight() {
    dropArea.classList.remove('drag-over');
}

dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;

    if (files.length > 0) {
        fileInput.files = files;
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }
}

// Click on drop area to open file dialog
dropArea.addEventListener('click', function () {
    fileInput.click();
});

// Event listeners
openModalBtn.addEventListener('click', openModal);

// Close modal when clicking outside
modal.addEventListener('click', function (e) {
    if (e.target === modal || e.target.classList.contains('modal-overlay')) {
        closeModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('show')) {
        closeModal();
    }
});
