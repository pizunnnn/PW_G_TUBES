document.addEventListener('DOMContentLoaded', function() {
const wrapper = document.getElementById('product-wrapper');
const gridBtn = document.getElementById('toggleGrid');
const listBtn = document.getElementById('toggleList');

// Fitur Toggle Grid/List
listBtn.addEventListener('click', () => {
wrapper.classList.remove('row-cols-2', 'row-cols-md-3', 'row-cols-lg-4');
wrapper.classList.add('row-cols-1');
// Menambahkan class list-view untuk styling khusus jika diperlukan
wrapper.querySelectorAll('.product-item').forEach(el => el.classList.add('list-mode'));
});

gridBtn.addEventListener('click', () => {
wrapper.classList.add('row-cols-2', 'row-cols-md-3', 'row-cols-lg-4');
wrapper.classList.remove('row-cols-1');
wrapper.querySelectorAll('.product-item').forEach(el => el.classList.remove('list-mode'));
});

// Simulasi Live Search (Frontend Only)
document.getElementById('liveSearch').addEventListener('keyup', function(e) {
let keyword = e.target.value.toLowerCase();
let items = document.querySelectorAll('.product-item');

items.forEach(item => {
let title = item.querySelector('.card-title').innerText.toLowerCase();
item.style.display = title.includes(keyword) ? 'block' : 'none';
});
});
});
