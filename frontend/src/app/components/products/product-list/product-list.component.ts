import { Component, type OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import {
  ProductService,
  type Product,
} from '../../../services/product.service';
import { LoadingComponent } from '../../shared/loading/loading.component';
import { ProductDetailModalComponent } from '../product-detail-modal/product-detail-modal.component';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    FormsModule,
    LoadingComponent,
    ProductDetailModalComponent,
  ],
  templateUrl: './product-list.component.html',
  styleUrls: ['./product-list.component.css'],
})
export class ProductListComponent implements OnInit {
  public productService = inject(ProductService);

  // Datos originales del servidor
  allProducts: Product[] = [];
  filteredProducts: Product[] = [];

  // Datos para mostrar en la tabla (paginados)
  products: Product[] = [];

  loading = false;
  currentPage = 1;
  totalPages = 1;
  totalProducts = 0;
  itemsPerPage = 10;

  // Opciones para items por página
  itemsPerPageOptions = [5, 10, 15, 25, 50, 100];

  // Modal properties
  selectedProduct: Product | null = null;
  isModalVisible = false;

  filters = {
    search: '',
    category: '',
    type: '',
  };

  ngOnInit() {
    this.loadProducts();
  }

  loadProducts() {
    this.loading = true;

    // Cargar todos los productos sin paginación del servidor
    const queryFilters: any = {};

    this.productService.getAllProducts(queryFilters).subscribe({
      next: (response) => {
        // Guardar todos los productos
        this.allProducts = response.data.data || response.data || [];

        // Aplicar filtros y paginación
        this.applyFiltersAndPagination();
        this.loading = false;
      },
      error: (error) => {
        console.error('Error cargando productos:', error);
        alert('Error al cargar los productos');
        this.loading = false;
      },
    });
  }

  applyFiltersAndPagination() {
    // Paso 1: Aplicar filtros
    this.filteredProducts = this.allProducts.filter((product) => {
      let matchesSearch = true;
      let matchesCategory = true;
      let matchesType = true;

      // Filtro de búsqueda
      if (this.filters.search) {
        const searchTerm = this.filters.search.toLowerCase();
        matchesSearch =
          (typeof product.name === 'string' &&
            product.name.toLowerCase().includes(searchTerm)) ||
          (typeof product.code === 'string' &&
            product.code.toLowerCase().includes(searchTerm)) ||
          (typeof product.description === 'string' &&
            product.description.toLowerCase().includes(searchTerm));
      }

      // Filtro de categoría
      if (this.filters.category) {
        matchesCategory = product.category === this.filters.category;
      }

      // Filtro de tipo
      if (this.filters.type) {
        matchesType = product.type === this.filters.type;
      }

      return matchesSearch && matchesCategory && matchesType;
    });

    // Paso 2: Calcular paginación
    this.totalProducts = this.filteredProducts.length;
    this.totalPages = Math.ceil(this.totalProducts / this.itemsPerPage);

    // Asegurar que currentPage esté en rango válido
    if (this.currentPage > this.totalPages) {
      this.currentPage = Math.max(1, this.totalPages);
    }

    // Paso 3: Aplicar paginación
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.products = this.filteredProducts.slice(startIndex, endIndex);
  }

  onFilterChange() {
    this.currentPage = 1;
    this.applyFiltersAndPagination();
  }

  clearFilters() {
    this.filters = {
      search: '',
      category: '',
      type: '',
    };
    this.currentPage = 1;
    this.applyFiltersAndPagination();
  }

  hasFilters(): boolean {
    return !!(
      this.filters.search ||
      this.filters.category ||
      this.filters.type
    );
  }

  changePage(page: number) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.applyFiltersAndPagination();
    }
  }

  // Método para cambiar items por página
  onItemsPerPageChange() {
    this.currentPage = 1; // Resetear a la primera página
    this.applyFiltersAndPagination();
  }

  // Navigation method
  goToDashboard() {
    window.location.href = '/dashboard';
  }

  // Pagination helper methods
  getPageNumbers(): number[] {
    const pages: number[] = [];
    const maxVisiblePages = 5;
    let startPage = Math.max(
      1,
      this.currentPage - Math.floor(maxVisiblePages / 2)
    );
    let endPage = Math.min(this.totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage + 1 < maxVisiblePages) {
      startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
      pages.push(i);
    }

    return pages;
  }

  // Métodos para cálculos de paginación
  getStartItem(): number {
    if (this.totalProducts === 0) return 0;
    return (this.currentPage - 1) * this.itemsPerPage + 1;
  }

  getEndItem(): number {
    return Math.min(this.currentPage * this.itemsPerPage, this.totalProducts);
  }

  // Método para obtener el rango de páginas para salto rápido
  getPageRanges(): { label: string; page: number }[] {
    const ranges = [];
    const totalPages = this.totalPages;

    if (totalPages <= 10) return [];

    // Agregar rangos cada 10 páginas
    for (let i = 10; i < totalPages; i += 10) {
      ranges.push({
        label: `Página ${i}`,
        page: i,
      });
    }

    // Agregar la última página si no está incluida
    if (totalPages % 10 !== 0) {
      ranges.push({
        label: `Página ${totalPages}`,
        page: totalPages,
      });
    }

    return ranges;
  }

  // Modal methods
  viewProduct(product: Product) {
    this.selectedProduct = product;
    this.isModalVisible = true;
  }

  closeModal() {
    this.isModalVisible = false;
    this.selectedProduct = null;
  }

  editProductFromModal(product: Product) {
    this.closeModal();
    // Navigate to edit page
    window.location.href = `/products/edit/${product.id}`;
  }

  deleteProduct(product: Product) {
    const confirmDelete = confirm(
      `¿Estás seguro de que quieres eliminar el producto "${product.name}"?\n\nEsta acción no se puede deshacer.`
    );

    if (confirmDelete) {
      this.productService.deleteProduct(product.id).subscribe({
        next: () => {
          alert('Producto eliminado exitosamente');
          // Recargar todos los productos después de eliminar
          this.loadProducts();
        },
        error: (error) => {
          console.error('Error eliminando producto:', error);
          alert('Error al eliminar el producto');
        },
      });
    }
  }

  onImageError(event: any) {
    event.target.src = '/placeholder.svg?height=60&width=60&text=Sin+Imagen';
  }

  truncateText(text: string, maxLength: number): string {
    if (!text) return '';
    return text.length > maxLength
      ? text.substring(0, maxLength) + '...'
      : text;
  }
}
