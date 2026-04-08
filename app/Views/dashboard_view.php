<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= $nama_startup ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { margin-bottom: 20px; }
        .stok-barang { font-weight: bold; color: #28a745; }
        .stok-low { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-primary">Dashboard <?= $nama_startup ?></h1>
                    <a href="/auth/logout" class="btn btn-danger">Logout</a>
                </div>
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Jual Sampah -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Jual Sampah Daur Ulang</h5>
                    </div>
                    <div class="card-body">
                        <form id="sellForm" class="row g-3">
                            <div class="col-md-4">
                                <label for="wasteType" class="form-label">Jenis Sampah</label>
                                <select class="form-select" id="wasteType" required>
                                    <option value="">Pilih Jenis Sampah</option>
                                    <!-- Options will be loaded by JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="quantity" class="form-label">Jumlah (kg)</label>
                                <input type="number" class="form-control" id="quantity" min="0.1" step="0.1" placeholder="0.0" required>
                            </div>
                            <div class="col-md-3">
                                <label for="totalPrice" class="form-label">Total Harga</label>
                                <input type="text" class="form-control" id="totalPrice" readonly placeholder="Rp 0">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-info w-100">Jual</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Katalog Produk -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Katalog Sampah Daur Ulang</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="productsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Harga (per kg)</th>
                                        <th>Stok (kg)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Products will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" class="row g-3">
                        <input type="hidden" id="editProductId">
                        <div class="col-12">
                            <input type="text" class="form-control" id="editProductName" required>
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control" id="editProductPrice" required>
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control" id="editProductStock" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveEditBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        class ProductManager {
            constructor() {
                this.wasteTypes = this.getWasteTypes();
                this.products = this.loadProducts();
                this.nextId = this.getNextId();
                this.init();
            }

            getWasteTypes() {
                return [
                    { id: 1, name: 'Botol Plastik', price: 1000 },
                    { id: 2, name: 'Minyak Jelantah', price: 5000 },
                    { id: 3, name: 'Kardus Bekas', price: 3000 },
                    { id: 4, name: 'Kertas Koran', price: 2000 },
                    { id: 5, name: 'Kaleng Aluminium', price: 8000 }
                ];
            }

            init() {
                this.populateWasteTypeDropdown();
                this.renderProducts();
                this.bindEvents();
            }

            populateWasteTypeDropdown() {
                const select = document.getElementById('wasteType');
                if (!select) {
                    return;
                }

                select.innerHTML = '<option value="" disabled selected>Pilih Jenis Sampah</option>';

                this.wasteTypes.forEach(type => {
                    const option = document.createElement('option');
                    option.value = type.id;
                    option.textContent = `${type.name} - Rp ${this.formatRupiah(type.price)}/kg`;
                    select.appendChild(option);
                });

                select.disabled = false;
            }

            formatRupiah(amount) {
                return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            calculateTotalPrice() {
                const wasteTypeId = document.getElementById('wasteType').value;
                const quantity = parseFloat(document.getElementById('quantity').value) || 0;
                const totalPriceField = document.getElementById('totalPrice');

                if (!totalPriceField) {
                    return;
                }

                if (wasteTypeId && quantity > 0) {
                    const wasteType = this.wasteTypes.find(type => type.id === parseInt(wasteTypeId, 10));
                    if (wasteType) {
                        const total = wasteType.price * quantity;
                        totalPriceField.value = `Rp ${this.formatRupiah(total)}`;
                        return;
                    }
                }

                totalPriceField.value = 'Rp 0';
            }

            sellWaste(wasteTypeId, quantity) {
                const wasteType = this.wasteTypes.find(type => type.id === parseInt(wasteTypeId, 10));
                if (!wasteType) {
                    alert('Pilih jenis sampah terlebih dahulu.');
                    return;
                }
                if (quantity <= 0 || Number.isNaN(quantity)) {
                    alert('Masukkan jumlah yang valid.');
                    return;
                }

                const existingProduct = this.products.find(product => product.name === wasteType.name);
                if (existingProduct) {
                    existingProduct.stock += quantity;
                    existingProduct.price = wasteType.price;
                } else {
                    this.products.push({
                        id: this.getNextId(),
                        name: wasteType.name,
                        price: wasteType.price,
                        stock: quantity
                    });
                }

                this.saveProducts();
                this.renderProducts();

                const total = wasteType.price * quantity;
                alert(`Berhasil menjual ${quantity}kg ${wasteType.name} seharga Rp ${this.formatRupiah(total)}. Katalog sudah diperbarui.`);
            }

            loadProducts() {
                const stored = localStorage.getItem('kepulProducts');
                return stored ? JSON.parse(stored) : [
                    { id: 1, name: 'Plastik PET', price: 2000, stock: 50 },
                    { id: 2, name: 'Kardus', price: 1500, stock: 30 },
                    { id: 3, name: 'Botol Kaca', price: 500, stock: 20 }
                ];
            }

            getNextId() {
                return this.products.length > 0 ? Math.max(...this.products.map(p => p.id)) + 1 : 1;
            }

            renderProducts() {
                const tbody = document.getElementById('productsTableBody');
                if (!tbody) {
                    return;
                }

                tbody.innerHTML = '';

                this.products.forEach(product => {
                    const stockClass = product.stock < 10 ? 'stok-low' : 'stok-barang';
                    const row = `
                        <tr>
                            <td>${product.name}</td>
                            <td>Rp ${product.price.toLocaleString()}</td>
                            <td class="${stockClass}">${product.stock}</td>
                            <td>
                                <button class="btn btn-sm btn-warning me-1" onclick="productManager.editProduct(${product.id})">Edit</button>
                                <button class="btn btn-sm btn-danger me-1" onclick="productManager.deleteProduct(${product.id})">Hapus</button>
                                <button class="btn btn-sm btn-success" onclick="productManager.sellProduct(${product.id})">Jual</button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            }

            editProduct(id) {
                const product = this.products.find(p => p.id == id);
                if (!product) {
                    return;
                }
                document.getElementById('editProductId').value = product.id;
                document.getElementById('editProductName').value = product.name;
                document.getElementById('editProductPrice').value = product.price;
                document.getElementById('editProductStock').value = product.stock;
                const modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            }

            bindEvents() {
                const sellForm = document.getElementById('sellForm');
                if (sellForm) {
                    sellForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const wasteTypeId = document.getElementById('wasteType').value;
                        const quantity = parseFloat(document.getElementById('quantity').value);
                        this.sellWaste(wasteTypeId, quantity);
                        e.target.reset();
                        document.getElementById('totalPrice').value = 'Rp 0';
                    });
                }

                const wasteTypeSelect = document.getElementById('wasteType');
                if (wasteTypeSelect) {
                    wasteTypeSelect.addEventListener('change', () => this.calculateTotalPrice());
                }

                const quantityInput = document.getElementById('quantity');
                if (quantityInput) {
                    quantityInput.addEventListener('input', () => this.calculateTotalPrice());
                }

                const saveEditBtn = document.getElementById('saveEditBtn');
                if (saveEditBtn) {
                    saveEditBtn.addEventListener('click', () => {
                        const id = document.getElementById('editProductId').value;
                        const name = document.getElementById('editProductName').value;
                        const price = document.getElementById('editProductPrice').value;
                        const stock = document.getElementById('editProductStock').value;
                        this.updateProduct(id, name, price, stock);
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                        modal.hide();
                    });
                }
            }

            updateProduct(id, name, price, stock) {
                const product = this.products.find(p => p.id == id);
                if (!product) {
                    return;
                }
                product.name = name;
                product.price = parseInt(price, 10);
                product.stock = parseInt(stock, 10);
                this.saveProducts();
                this.renderProducts();
            }

            deleteProduct(id) {
                this.products = this.products.filter(p => p.id != id);
                this.saveProducts();
                this.renderProducts();
            }

            saveProducts() {
                localStorage.setItem('kepulProducts', JSON.stringify(this.products));
            }
        }

        let productManager;
        document.addEventListener('DOMContentLoaded', () => {
            productManager = new ProductManager();
            window.productManager = productManager;
        });
    </script>
</body>
</html>