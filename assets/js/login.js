document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('loginForm');
  if (!form) return;

  // Password validation function
  function validatePassword(password) {
    if (password.length < 8) {
      return 'Password must be at least 8 characters long.';
    }
    if (!/[A-Z]/.test(password)) {
      return 'Password must contain at least one uppercase letter.';
    }
    if (!/[a-z]/.test(password)) {
      return 'Password must contain at least one lowercase letter.';
    }
    if (!/[0-9]/.test(password)) {
      return 'Password must contain at least one number.';
    }
    return true;
  }

  form.addEventListener('submit', function (e) {
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    let err = '';

    if (!email.value.trim()) {
      err = 'Please enter your email address.';
    } else if (!password.value) {
      err = 'Please enter your password.';
    } else {
      const passwordValidation = validatePassword(password.value);
      if (passwordValidation !== true) {
        err = passwordValidation;
      }
    }

    if (err) {
      e.preventDefault();
      alert(err);
      (err === 'Please enter your email address.') ? email.focus() : password.focus();
    }
  });
});
