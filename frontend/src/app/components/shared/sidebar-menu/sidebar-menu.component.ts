import { Component, Input, Output, EventEmitter, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { AuthService } from '../../../services/auth.service';

@Component({
  selector: 'app-sidebar-menu',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './sidebar-menu.component.html',
  styleUrls: ['./sidebar-menu.component.css'],
})
export class SidebarMenuComponent {
  @Input() isOpen = false;
  @Output() close = new EventEmitter<void>();

  public authService = inject(AuthService);
  private router = inject(Router);

  menuItems = [
    {
      id: 'home',
      title: 'Página Principal',
      description: 'Volver al inicio',
      icon: 'home',
      action: () => this.goToHome(),
      show: true,
    },
    {
      id: 'dashboard',
      title: 'Dashboard',
      description: 'Panel de administración',
      icon: 'dashboard',
      action: () => this.goToDashboard(),
      show: true,
    },
    {
      id: 'products',
      title: 'Ver Productos',
      description: 'Gestionar inventario',
      icon: 'products',
      action: () => this.goToProducts(),
      show: true,
    },
    {
      id: 'new-product',
      title: 'Nuevo Producto',
      description: 'Agregar al inventario',
      icon: 'add',
      action: () => this.goToNewProduct(),
      show: true,
    },
    {
      id: 'orders',
      title: 'Gestionar Pedidos',
      description: 'Administrar órdenes',
      icon: 'orders',
      action: () => this.goToOrdersAdmin(),
      show: () => this.authService.isAdmin() || this.authService.isEmpleado(),
    },
    {
      id: 'users',
      title: 'Administrar Usuarios',
      description: 'Gestión de usuarios',
      icon: 'users',
      action: () => this.goToUserManagement(),
      show: () => this.authService.isAdmin(),
    },
  ];

  // Track by function for ngFor performance
  trackByFn(index: number, item: any): string {
    return item.id || index.toString();
  }

  closeMenu() {
    this.close.emit();
  }

  onBackdropClick() {
    this.closeMenu();
  }

  onMenuClick(event: Event) {
    event.stopPropagation();
  }

  executeAction(item: any) {
    item.action();
    this.closeMenu();
  }

  shouldShowItem(item: any): boolean {
    if (typeof item.show === 'function') {
      return item.show();
    }
    return item.show;
  }

  // Navigation methods
  goToHome() {
    this.router.navigate(['/']);
  }

  goToDashboard() {
    this.router.navigate(['/dashboard']);
  }

  goToProducts() {
    this.router.navigate(['/products']);
  }

  goToNewProduct() {
    this.router.navigate(['/products/new']);
  }

  goToOrdersAdmin() {
    this.router.navigate(['/admin/orders']);
  }

  goToUserManagement() {
    this.router.navigate(['/admin/users']);
  }
}
