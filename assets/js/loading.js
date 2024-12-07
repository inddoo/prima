// Global AJAX Loading Handler
let activeRequests = 0;

function showLoading() {
    const loading = document.getElementById('loading-screen');
    activeRequests++;
    loading.style.display = 'flex';
    loading.style.opacity = '1';
}

function hideLoading() {
    activeRequests--;
    if (activeRequests <= 0) {
        const loading = document.getElementById('loading-screen');
        loading.style.opacity = '0';
        setTimeout(() => {
            loading.style.display = 'none';
        }, 300);
        activeRequests = 0;
    }
}

// Intercept Fetch requests
const originalFetch = window.fetch;
window.fetch = function() {
    showLoading();
    return originalFetch.apply(this, arguments)
        .then(response => {
            hideLoading();
            return response;
        })
        .catch(error => {
            hideLoading();
            throw error;
        });
};

// Intercept XMLHttpRequest
const originalXHR = window.XMLHttpRequest;
window.XMLHttpRequest = function() {
    const xhr = new originalXHR();
    xhr.addEventListener('loadstart', showLoading);
    xhr.addEventListener('loadend', hideLoading);
    return xhr;
}; 