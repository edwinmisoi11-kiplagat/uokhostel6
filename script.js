document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("applicationForm");
  const applyButton = document.getElementById("applyButton");
  const message = document.getElementById("formMessage");
  const roomInput = document.getElementById("room_number");

  document.getElementById("currentYear").textContent = new Date().getFullYear();

  function showMessage(text, type = "info") {
    message.textContent = text;
    message.className = `form-message show ${type}`;
  }

  function validateForm() {
    // Use the browser's required/type/min/max validation first.
    if (!form.reportValidity()) return false;

    const room = Number(roomInput.value);
    if (!Number.isInteger(room) || room < 1 || room > 300) {
      roomInput.setCustomValidity("Enter a room number from 1 to 300.");
      roomInput.reportValidity();
      roomInput.setCustomValidity("");
      return false;
    }

    const phone = document.getElementById("phone_number").value.trim();
    // Accept common Kenyan formats and international numbers with 8–15 digits.
    const digits = phone.replace(/\D/g, "");
    if (digits.length < 9 || digits.length > 15) {
      showMessage("Please enter a valid phone number containing 9 to 15 digits.", "error");
      document.getElementById("phone_number").focus();
      return false;
    }
    return true;
  }

  applyButton.addEventListener("click", () => {
    if (validateForm()) {
      showMessage("Your details look complete. Review them, then select “Submit Application” to send your application for review.", "info");
    }
  });

  form.addEventListener("submit", (event) => {
    if (!validateForm()) {
      event.preventDefault();
      return;
    }
    const confirmed = window.confirm("Submit this Hostel 6 accommodation application for review?");
    if (!confirmed) event.preventDefault();
  });
});
