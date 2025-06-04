function loadData() {
  fetch('data.php')
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById('data-container');
      container.innerHTML = '';
      const labels = [];
      const values = [];

      data.forEach(item => {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `<h3>${item.title}</h3><p>${item.value}</p>`;
        container.appendChild(card);

        labels.push(item.title);
        values.push(parseFloat(item.value));
      });

      drawGraph(labels, values);
    });
}

function drawGraph(labels, values) {
  const canvas = document.getElementById('graph');
  const ctx = canvas.getContext('2d');

  ctx.clearRect(0, 0, canvas.width, canvas.height);
  const max = Math.max(...values);
  const barWidth = 50;
  const spacing = 70;

  values.forEach((val, i) => {
    const barHeight = (val / max) * 150;
    const x = i * spacing + 50;
    const y = 200 - barHeight;

    ctx.fillStyle = '#3498db';
    ctx.fillRect(x, y, barWidth, barHeight);

    ctx.fillStyle = 'black';
    ctx.fillText(labels[i], x, 210);
  });
}

window.onload = loadData;
