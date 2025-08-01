function toggleMenu() {
  const docks = document.querySelector('.docks');
  docks.classList.toggle('active');
}


emailjs.sendForm('your_service_ID', 'your_template_ID', this)