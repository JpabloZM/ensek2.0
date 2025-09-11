/**
 * Service Request Debug Helper
 * This file helps track form submission issues
 */

// Set up console logging wrapper
const originalLog = console.log;
const originalError = console.error;
const originalWarn = console.warn;

// Enhance console logging for tracking
console.log = function (...args) {
    document.getElementById("debug-log").innerHTML +=
        '<div class="text-success">' + args.join(" ") + "</div>";
    originalLog.apply(console, args);
};

console.error = function (...args) {
    document.getElementById("debug-log").innerHTML +=
        '<div class="text-danger"><strong>ERROR:</strong> ' +
        args.join(" ") +
        "</div>";
    originalError.apply(console, args);
};

console.warn = function (...args) {
    document.getElementById("debug-log").innerHTML +=
        '<div class="text-warning"><strong>WARNING:</strong> ' +
        args.join(" ") +
        "</div>";
    originalWarn.apply(console, args);
};

// Log when the script loads
console.log(
    "Service request debug script loaded at: " + new Date().toLocaleTimeString()
);

// Check if we have the needed form elements
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("serviceRequestForm");
    console.log("Form found: " + (form ? "Yes" : "No"));

    if (form) {
        // Log CSRF token availability
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        console.log("CSRF Token found: " + (csrfToken ? "Yes" : "No"));
        console.log("Form action: " + form.action);

        // Monitor the form submission button
        const submitBtn = document.getElementById("submitServiceRequest");
        if (submitBtn) {
            console.log("Submit button found");

            submitBtn.addEventListener("click", function () {
                console.log(
                    "Submit button clicked at: " +
                        new Date().toLocaleTimeString()
                );
            });
        } else {
            console.error("Submit button not found");
        }

        // Track form submissions
        form.addEventListener("submit", function (e) {
            console.log(
                "Form submission detected at: " +
                    new Date().toLocaleTimeString()
            );

            // Track the FormData values
            const formData = new FormData(form);
            console.log("Form data entries:");
            for (let [key, value] of formData.entries()) {
                console.log(`- ${key}: ${value}`);
            }
        });
    }
});

// Function to track fetch API calls
const originalFetch = window.fetch;
window.fetch = function (...args) {
    console.log("Fetch API called with URL: " + args[0]);
    if (args[1] && args[1].method) {
        console.log("Method: " + args[1].method);
    }

    return originalFetch
        .apply(this, args)
        .then((response) => {
            console.log("Fetch response received: " + response.status);
            console.log("Response URL: " + response.url);
            console.log("Response redirected: " + response.redirected);
            console.log("Response OK: " + response.ok);

            // Clone the response so we can both log it and return it
            const clonedResponse = response.clone();

            // Only try to parse as JSON if it's expected to be JSON
            if (
                response.headers.get("content-type") &&
                response.headers
                    .get("content-type")
                    .includes("application/json")
            ) {
                clonedResponse
                    .json()
                    .then((data) => {
                        console.log("Response JSON data:", data);
                    })
                    .catch((err) => {
                        console.error("Error parsing JSON response:", err);
                    });
            }

            return response;
        })
        .catch((error) => {
            console.error("Fetch error: " + error);
            throw error;
        });
};
