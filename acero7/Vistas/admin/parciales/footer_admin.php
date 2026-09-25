  </main>
</div>

<footer class="admin-footer">
  <span>ACERO GYM — Panel de administración</span>
  <span><?= date('d/m/Y H:i') ?></span>
</footer>

<script>
// Menú lateral desplegable en pantallas chicas.
document.getElementById('adminBurger')?.addEventListener('click', function () {
  document.getElementById('adminSidebar')?.classList.toggle('is-open');
});

// Confirmación antes de eliminar cualquier registro.
document.querySelectorAll('form[data-confirmar]').forEach(function (form) {
  form.addEventListener('submit', function (e) {
    if (!confirm(form.dataset.confirmar)) {
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>
