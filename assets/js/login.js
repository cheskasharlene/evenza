document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('loginForm');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    let err = '';

    if (!email.value.trim()) err = 'Please enter your email address.';
    else if (!password.value) err = 'Please enter your password.';

    if (err) {
      e.preventDefault();
      alert(err);
      (err === 'Please enter your email address.') ? email.focus() : password.focus();
    }
  });
});
