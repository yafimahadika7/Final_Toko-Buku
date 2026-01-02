(function(){
  // Chart on dashboard (optional)
  const canvas = document.getElementById('salesChart');
  if (!canvas) return;

  const daysSelect = document.getElementById('chartDays');
  let chart;

  function formatRp(n){
    try { return new Intl.NumberFormat('id-ID').format(n); } catch(e){ return n; }
  }

  async function load(days){
    const res = await fetch(`${window.BASE_URL || ''}/api/dashboard_sales.php?days=${days}`);
    const data = await res.json();
    const labels = data.map(x => x.d);
    const values = data.map(x => Number(x.t));

    if (chart) chart.destroy();
    chart = new Chart(canvas, {
      type: 'line',
      data: { labels, datasets: [{ label:'Sales (Rp)', data: values, tension: .35, borderWidth: 2, pointRadius: 2 }]},
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: true } },
        scales: { y: { ticks: { callback: v => formatRp(v) } } }
      }
    });
  }

  const initial = daysSelect ? daysSelect.value : 7;
  load(initial);

  daysSelect?.addEventListener('change', () => load(daysSelect.value));
})();