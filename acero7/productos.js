// ===== Modal de detalle de producto (tienda) =====
(function () {
  const modal = document.getElementById('productModal');
  if (!modal) return;

  const imgBox = document.getElementById('productModalImage');
  const catEl = document.getElementById('productModalCategoria');
  const titleEl = document.getElementById('productModalTitle');
  const marcaEl = document.getElementById('productModalMarca');
  const descEl = document.getElementById('productModalDesc');
  const featuresEl = document.getElementById('productModalFeatures');
  const stockEl = document.getElementById('productModalStock');
  const precioEl = document.getElementById('productModalPrecio');
  const idInput = document.getElementById('productModalId');

  function abrirModal(card) {
    const nombre = card.dataset.nombre || '';
    const categoria = card.dataset.categoria || '';
    const marca = card.dataset.marca || '';
    const precio = card.dataset.precio || '';
    const stock = card.dataset.stock || '0';
    const descripcion = card.dataset.descripcion || '';
    const caracteristicas = (card.dataset.caracteristicas || '').split('|').filter(Boolean);
    const imagen = card.dataset.imagen || '';
    const id = card.dataset.id || '';

    catEl.textContent = categoria;
    titleEl.textContent = nombre;
    marcaEl.textContent = marca;
    marcaEl.style.display = marca ? '' : 'none';
    descEl.textContent = descripcion || 'Sin descripción disponible para este producto.';
    stockEl.textContent = 'Stock disponible: ' + stock;
    precioEl.textContent = '$' + precio;
    idInput.value = id;

    featuresEl.innerHTML = '';
    caracteristicas.forEach((item) => {
      const li = document.createElement('li');
      li.textContent = item;
      featuresEl.appendChild(li);
    });
    featuresEl.style.display = caracteristicas.length ? '' : 'none';

    if (imagen) {
      imgBox.innerHTML = '<img src="' + imagen + '" alt="' + nombre.replace(/"/g, '') + '">';
    } else {
      imgBox.innerHTML = '<div class="shop-no-image">ACERO</div>';
    }

    modal.hidden = false;
    document.body.classList.add('modal-open');
  }

  function cerrarModal() {
    modal.hidden = true;
    document.body.classList.remove('modal-open');
  }

  document.querySelectorAll('.shop-card').forEach((card) => {
    card.addEventListener('click', (e) => {
      // No abrir el modal si el click fue sobre el botón/form de "Agregar al carrito"
      if (e.target.closest('.shop-card__form')) return;
      abrirModal(card);
    });
    card.addEventListener('keydown', (e) => {
      if ((e.key === 'Enter' || e.key === ' ') && !e.target.closest('.shop-card__form')) {
        e.preventDefault();
        abrirModal(card);
      }
    });
  });

  modal.querySelectorAll('[data-close-modal]').forEach((el) => {
    el.addEventListener('click', cerrarModal);
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hidden) cerrarModal();
  });
})();
