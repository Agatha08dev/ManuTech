const senhaInput = document.getElementById('senha');
const toggleBtn = document.createElement('button');
toggleBtn.type = 'button';
toggleBtn.textContent = 'Mostrar';
toggleBtn.style.marginLeft = '10px';

senhaInput.parentNode.insertBefore(toggleBtn, senhaInput.nextSibling);

toggleBtn.addEventListener('click', function() {
  if (senhaInput.type === 'password') {
    senhaInput.type = 'text';
    toggleBtn.textContent = 'Ocultar';
  } else {
    senhaInput.type = 'password';
    toggleBtn.textContent = 'Mostrar';
  }
});
