import { Component, type OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { AuthService, type User } from '../../services/auth.service';
import { ProductService } from '../../services/product.service';
import { UserService } from '../../services/user.service';
import { LoadingComponent } from '../shared/loading/loading.component';
import { SidebarMenuComponent } from '../shared/sidebar-menu/sidebar-menu.component';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule, LoadingComponent, SidebarMenuComponent],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css'],
})
export class DashboardComponent implements OnInit {
  public authService = inject(AuthService);
  private productService = inject(ProductService);
  private userService = inject(UserService);
  private router = inject(Router);

  currentUser: User | null = null;
  products: any[] = [];
  users: any[] = [];
  loading = true;
  isSidebarOpen = false;

  stats = {
    totalProducts: 0,
    totalUsers: 0,
    lowStock: 0,
  };

  recentProducts: any[] = [];
  recentUsers: any[] = [];

  ngOnInit(): void {
    this.currentUser = this.authService.getCurrentUserData();
    this.loadDashboardData();
  }

  // Toggle sidebar
  toggleSidebar() {
    this.isSidebarOpen = !this.isSidebarOpen;
  }

  closeSidebar() {
    this.isSidebarOpen = false;
  }

  loadDashboardData() {
    this.loading = true;

    // Load products
    this.productService.getAllProducts().subscribe({
      next: (response: any) => {
        this.products = response.data?.data || [];
        this.recentProducts = this.products.slice(0, 5);
        this.stats.totalProducts = this.products.length;
        this.stats.lowStock = this.products.filter(
          (p) => (p.quantity || 0) < 10
        ).length;
        this.checkLoadingComplete();
      },
      error: (error: any) => {
        console.error('Error cargando productos:', error);
        this.setMockData();
        this.checkLoadingComplete();
      },
    });

    // Load users (only for admin)
    if (this.authService.isAdmin()) {
      this.userService.getAllUsers().subscribe({
        next: (response: any) => {
          this.users = response.data?.data || [];
          this.recentUsers = this.users.slice(0, 5);
          this.stats.totalUsers = this.users.length;
          this.checkLoadingComplete();
        },
        error: (error: any) => {
          console.error('Error cargando usuarios:', error);
          this.checkLoadingComplete();
        },
      });
    } else {
      this.checkLoadingComplete();
    }
  }

  setMockData() {
    this.stats = {
      totalProducts: 24,
      totalUsers: 156,
      lowStock: 3,
    };

    this.recentProducts = [
      {
        id: '1',
        name: 'Vestido XV Años Elegante',
        price: 2500,
        quantity: 5,
        category: 'XV Años',
      },
      {
        id: '2',
        name: 'Conjunto Bautizo Clásico',
        price: 800,
        quantity: 10,
        category: 'Bautizos',
      },
      {
        id: '3',
        name: 'Vestido XV Años Moderno',
        price: 3200,
        quantity: 3,
        category: 'XV Años',
      },
    ];

    this.recentUsers = [
      {
        id: '1',
        name: 'Juan',
        lastName: 'Pérez',
        email: 'juan@example.com',
        role: 'cliente',
      },
      {
        id: '2',
        name: 'María',
        lastName: 'García',
        email: 'maria@example.com',
        role: 'empleado',
      },
      {
        id: '3',
        name: 'Carlos',
        lastName: 'López',
        email: 'carlos@example.com',
        role: 'cliente',
      },
    ];
  }

  private checkLoadingComplete() {
    setTimeout(() => {
      this.loading = false;
    }, 800);
  }

  getGreeting(): string {
    const hour = new Date().getHours();
    if (hour < 12) return 'Buenos días';
    if (hour < 18) return 'Buenas tardes';
    return 'Buenas noches';
  }

  getInitials(name: string): string {
    if (!name) return 'U';
    return name
      .split(' ')
      .map((n) => n[0])
      .join('')
      .toUpperCase()
      .substring(0, 2);
  }

  // Navigation methods
  goToHome() {
    this.router.navigate(['/']);
  }

  goToProducts() {
    this.router.navigate(['/products']);
  }

  goToNewProduct() {
    this.router.navigate(['/products/new']);
  }

  goToUserManagement() {
    this.router.navigate(['/admin/users']);
  }

  goToOrdersAdmin() {
    this.router.navigate(['/admin/orders']);
  }

  logout() {
    this.authService.logout().subscribe({
      next: () => {
        this.router.navigate(['/login']);
      },
      error: () => {
        this.router.navigate(['/login']);
      },
    });
  }

  trackByFn(index: number, item: any) {
    return item.id || index;
  }
}
