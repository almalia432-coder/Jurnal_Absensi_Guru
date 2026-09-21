let currentStep = 1;

function goToStep(step) {
  if (step > currentStep) return;
  currentStep = step;
  updateUI();
}

function nextStep(step) {
  currentStep = step;
  updateUI();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateUI() {
  [1, 2, 3].forEach(i => {
    document.getElementById('section-' + i).classList.toggle('active', i === currentStep);
    const nav = document.getElementById('step-nav-' + i);
    nav.classList.remove('active', 'done');
    if (i === currentStep) nav.classList.add('active');
    else if (i < currentStep) nav.classList.add('done');
    const dot = nav.querySelector('.step-dot');
    dot.innerHTML = i < currentStep ? '<i class="bi bi-check2" style="font-size:13px"></i>' : i;
  });

  const pct = Math.round((currentStep / 3) * 100);
  document.getElementById('progress-fill').style.width = pct + '%';
  document.getElementById('step-label-text').textContent = 'Langkah ' + currentStep + ' dari 3';
  document.getElementById('step-pct-text').textContent = pct + '%';
}

function changeJam(d) {
  const el = document.getElementById('jam-ke');
  el.value = Math.max(1, Math.min(12, parseInt(el.value || 1) + d));
}

function changeCount(type, d) {
  const el = document.getElementById('count-' + type);
  el.value = Math.max(0, parseInt(el.value || 0) + d);
}

function selectStatus(type) {
  ['hadir','izin','sakit','dinas'].forEach(s => {
    const el = document.getElementById('sc-' + s);
    if (el) el.classList.remove('selected-hadir','selected-izin','selected-sakit','selected-dinas');
  });
  const target = document.getElementById('sc-' + type);
  if (target) target.classList.add('selected-' + type);
}

// Set today's date
const now = new Date();
document.getElementById('today-date').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
document.getElementById('date-input').value = now.toISOString().split('T')[0];