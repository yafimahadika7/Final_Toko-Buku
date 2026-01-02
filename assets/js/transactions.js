(() => {
  const cartTbody = document.querySelector('#cartTable tbody');
  const emptyRow = document.getElementById('cartEmptyRow');
  const hiddenInputs = document.getElementById('hiddenInputs');
  const totalEl = document.getElementById('cartTotal');

  if (!cartTbody || !hiddenInputs || !totalEl) return;

  const cart = new Map(); // productId -> {name, price, qty, stock}

  function formatRp(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
  }

  function render() {
    // clear rows except empty placeholder
    cartTbody.querySelectorAll('tr[data-row]').forEach(r => r.remove());

    let total = 0;
    hiddenInputs.innerHTML = '';

    if (cart.size === 0) {
      emptyRow.style.display = '';
      totalEl.textContent = formatRp(0);
      return;
    }
    emptyRow.style.display = 'none';

    for (const [id, item] of cart.entries()) {
      const sub = item.price * item.qty;
      total += sub;

      const tr = document.createElement('tr');
      tr.dataset.row = '1';
      tr.className = 'hover:bg-slate-950/40';
      tr.innerHTML = `
        <td class="py-3 font-medium">${item.name}</td>
        <td class="py-3 text-right">
          <input type="number" min="1" max="${item.stock}" value="${item.qty}"
            class="qty-input w-20 text-right rounded-xl bg-slate-950 border border-slate-800/70 px-3 py-2 text-sm"
            data-id="${id}">
        </td>
        <td class="py-3 text-right font-semibold">${formatRp(sub)}</td>
        <td class="py-3 text-right">
          <button type="button"
            class="remove-btn h-9 w-9 rounded-xl bg-slate-950 border border-slate-800/70 hover:bg-slate-800"
            data-id="${id}">×</button>
        </td>
      `;
      cartTbody.appendChild(tr);

      // hidden input for POST
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = `qty[${id}]`;
      input.value = String(item.qty);
      input.dataset.hidden = String(id);
      hiddenInputs.appendChild(input);
    }

    totalEl.textContent = formatRp(total);
  }

  document.querySelectorAll('.add-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      const name = btn.dataset.name;
      const price = Number(btn.dataset.price);
      const stock = Number(btn.dataset.stock);

      if (!id) return;

      const existing = cart.get(id);
      if (!existing) {
        cart.set(id, { name, price, qty: 1, stock });
      } else {
        if (existing.qty < stock) existing.qty += 1;
      }
      render();
    });
  });

  cartTbody.addEventListener('input', (e) => {
    const t = e.target;
    if (!(t instanceof HTMLInputElement) || !t.classList.contains('qty-input')) return;

    const id = t.dataset.id;
    const item = cart.get(id);
    if (!item) return;

    let v = Number(t.value || 1);
    if (v < 1) v = 1;
    if (v > item.stock) v = item.stock;

    item.qty = v;

    // update hidden input
    const hidden = hiddenInputs.querySelector(`input[data-hidden="${id}"]`);
    if (hidden) hidden.value = String(v);

    render();
  });

  cartTbody.addEventListener('click', (e) => {
    const t = e.target;
    if (!(t instanceof HTMLElement)) return;

    const remove = t.closest('.remove-btn');
    if (!remove) return;

    const id = remove.getAttribute('data-id');
    cart.delete(id);
    render();
  });

  // Category filter -> reload with ?cat=
  const filter = document.getElementById('categoryFilter');
  filter?.addEventListener('change', () => {
    const cat = filter.value;
    const url = new URL(window.location.href);
    if (cat) url.searchParams.set('cat', cat);
    else url.searchParams.delete('cat');
    window.location.href = url.toString();
  });
})();
