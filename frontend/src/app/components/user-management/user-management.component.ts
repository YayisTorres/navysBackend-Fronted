import { Component, type OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import {
  FormsModule,
  ReactiveFormsModule,
  FormBuilder,
  type FormGroup,
  Validators,
} from '@angular/forms';
import { AuthService, type User } from '../../services/auth.service';
import { UserService } from '../../services/user.service';
import { LoadingComponent } from '../shared/loading/loading.component';
import { HeaderComponent } from '../shared/header/header.component';
import { UserDetailModalComponent } from '../users/user-detail-modal/user-detail-modal.component';

@Component({
  selector: 'app-user-management',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    FormsModule,
    ReactiveFormsModule,
    LoadingComponent,
    HeaderComponent,
    UserDetailModalComponent,
  ],
  templateUrl: './user-management.component.html',
  styleUrls: ['./user-management.component.css'],
})
export class UserManagementComponent implements OnInit {
  private authService = inject(AuthService);
  private userService = inject(UserService);
  private router = inject(Router);
  private fb = inject(FormBuilder);

  // Datos originales del servidor
  allUsers: User[] = [];
  filteredUsers: User[] = [];

  // Datos para mostrar en la tabla (paginados)
  users: User[] = [];

  loading = true;
  showModal = false;
  editingUser: User | null = null;
  isCreating = false;
  searchTerm = '';
  selectedRole = '';

  // Paginación
  currentPage = 1;
  totalPages = 1;
  totalUsers = 0;
  itemsPerPage = 10;
  itemsPerPageOptions = [5, 10, 15, 25, 50];

  // Modal properties for user details
  selectedUser: User | null = null;
  isDetailModalVisible = false;

  userForm: FormGroup = this.fb.group({
    name: ['', [Validators.required, Validators.minLength(2)]],
    lastName: ['', [Validators.required, Validators.minLength(2)]],
    middleName: [''],
    email: ['', [Validators.required, Validators.email]],
    password: [''],
    numberPhone: [''],
    role: ['cliente', [Validators.required]],
    photoFile: [null],
  });

  selectedFile: File | null = null;
  imagePreview: string | null = null;
  formErrors: any = {};

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(): void {
    this.loading = true;
    // Cargar todos los usuarios sin paginación del servidor
    this.userService.getAllUsers().subscribe({
      next: (response: any) => {
        if (response.success) {
          // Manejar diferentes estructuras de respuesta
          this.allUsers = response.data?.data || response.data || [];
          this.applyFiltersAndPagination();
        }
        this.loading = false;
      },
      error: (error: any) => {
        console.error('Error cargando usuarios:', error);
        this.loading = false;
      },
    });
  }

  applyFiltersAndPagination(): void {
    // Paso 1: Aplicar filtros
    this.filteredUsers = this.allUsers.filter((user) => {
      const matchesSearch =
        !this.searchTerm ||
        user.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        user.lastName.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        user.email.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
        (user.numberPhone && user.numberPhone.includes(this.searchTerm));

      const matchesRole = !this.selectedRole || user.role === this.selectedRole;

      return matchesSearch && matchesRole;
    });

    // Paso 2: Calcular paginación
    this.totalUsers = this.filteredUsers.length;
    this.totalPages = Math.ceil(this.totalUsers / this.itemsPerPage);

    // Asegurar que currentPage esté en rango válido
    if (this.currentPage > this.totalPages) {
      this.currentPage = Math.max(1, this.totalPages);
    }

    // Paso 3: Aplicar paginación
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const endIndex = startIndex + this.itemsPerPage;
    this.users = this.filteredUsers.slice(startIndex, endIndex);
  }

  onSearchChange(): void {
    this.currentPage = 1;
    this.applyFiltersAndPagination();
  }

  onRoleFilterChange(): void {
    this.currentPage = 1;
    this.applyFiltersAndPagination();
  }

  // Método para cambiar items por página
  onItemsPerPageChange(): void {
    this.currentPage = 1;
    this.applyFiltersAndPagination();
  }

  // Métodos de paginación
  changePage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
      this.applyFiltersAndPagination();
    }
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

  getStartItem(): number {
    if (this.totalUsers === 0) return 0;
    return (this.currentPage - 1) * this.itemsPerPage + 1;
  }

  getEndItem(): number {
    return Math.min(this.currentPage * this.itemsPerPage, this.totalUsers);
  }

  getPageRanges(): { label: string; page: number }[] {
    const ranges = [];
    const totalPages = this.totalPages;

    if (totalPages <= 10) return [];

    for (let i = 10; i < totalPages; i += 10) {
      ranges.push({
        label: `Página ${i}`,
        page: i,
      });
    }

    if (totalPages % 10 !== 0) {
      ranges.push({
        label: `Página ${totalPages}`,
        page: totalPages,
      });
    }

    return ranges;
  }

  // Modal methods for user details
  viewUser(user: User) {
    this.selectedUser = user;
    this.isDetailModalVisible = true;
  }

  closeDetailModal() {
    this.isDetailModalVisible = false;
    this.selectedUser = null;
  }

  editUserFromModal(user: User) {
    this.closeDetailModal();
    this.openEditModal(user);
  }

  openCreateModal(): void {
    this.isCreating = true;
    this.editingUser = null;
    this.showModal = true;
    this.resetForm();
    this.userForm
      .get('password')
      ?.setValidators([Validators.required, Validators.minLength(6)]);
    this.userForm.get('password')?.updateValueAndValidity();
  }

  openEditModal(user: User): void {
    this.isCreating = false;
    this.editingUser = user;
    this.showModal = true;
    this.resetForm();

    this.userForm.patchValue({
      name: user.name,
      lastName: user.lastName,
      middleName: user.middleName || '',
      email: user.email,
      numberPhone: user.numberPhone || '',
      role: user.role,
    });

    this.userForm.get('password')?.clearValidators();
    this.userForm.get('password')?.updateValueAndValidity();

    if (user.photo) {
      this.imagePreview = this.getFullImageUrl(user.photo);
    }
  }

  closeModal(): void {
    this.showModal = false;
    this.editingUser = null;
    this.isCreating = false;
    this.resetForm();
  }

  resetForm(): void {
    this.userForm.reset({
      role: 'cliente',
    });
    this.selectedFile = null;
    this.imagePreview = null;
    this.formErrors = {};
  }

  onFileSelected(event: any): void {
    const file = event.target.files[0];
    if (file) {
      const allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/gif',
      ];
      if (!allowedTypes.includes(file.type)) {
        alert('Solo se permiten archivos de imagen (JPG, PNG, GIF)');
        return;
      }

      if (file.size > 2 * 1024 * 1024) {
        alert('El archivo debe ser menor a 2MB');
        return;
      }

      this.selectedFile = file;

      const reader = new FileReader();
      reader.onload = (e: any) => {
        this.imagePreview = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  removeImage(): void {
    this.selectedFile = null;
    this.imagePreview = null;
    const fileInput = document.getElementById('photoFile') as HTMLInputElement;
    if (fileInput) {
      fileInput.value = '';
    }
  }

  onSubmit(): void {
    if (this.userForm.valid) {
      const formData = this.userForm.value;

      if (this.isCreating) {
        this.createUser(formData);
      } else if (this.editingUser) {
        this.updateUser(this.editingUser.id, formData);
      }
    } else {
      this.markFormGroupTouched();
    }
  }

  createUser(userData: any): void {
    this.userService.createUser(userData, this.selectedFile).subscribe({
      next: (response: any) => {
        if (response.success) {
          alert('Usuario creado exitosamente');
          this.closeModal();
          this.loadUsers();
        }
      },
      error: (error: any) => {
        console.error('Error creando usuario:', error);
        this.handleFormErrors(error);
      },
    });
  }

  updateUser(userId: number, userData: any): void {
    if (!userData.password) {
      delete userData.password;
    }

    this.userService.updateUser(userId, userData, this.selectedFile).subscribe({
      next: (response: any) => {
        if (response.success) {
          alert('Usuario actualizado exitosamente');
          this.closeModal();
          this.loadUsers();
        }
      },
      error: (error: any) => {
        console.error('Error actualizando usuario:', error);
        this.handleFormErrors(error);
      },
    });
  }

  deleteUser(user: User): void {
    if (
      confirm(
        `¿Estás seguro de eliminar al usuario ${user.name} ${user.lastName}?`
      )
    ) {
      this.userService.deleteUser(user.id).subscribe({
        next: (response: any) => {
          if (response.success) {
            alert('Usuario eliminado exitosamente');
            this.loadUsers();
          }
        },
        error: (error: any) => {
          console.error('Error eliminando usuario:', error);
          alert('Error al eliminar usuario');
        },
      });
    }
  }

  handleFormErrors(error: any): void {
    if (error.status === 422 && error.error.errors) {
      this.formErrors = error.error.errors;
    } else {
      alert('Error al procesar la solicitud');
    }
  }

  markFormGroupTouched(): void {
    Object.keys(this.userForm.controls).forEach((key) => {
      this.userForm.get(key)?.markAsTouched();
    });
  }

  getFieldError(fieldName: string): string {
    const control = this.userForm.get(fieldName);
    if (control?.errors && control.touched) {
      if (control.errors['required']) return `${fieldName} es requerido`;
      if (control.errors['email']) return 'Email inválido';
      if (control.errors['minlength'])
        return `Mínimo ${control.errors['minlength'].requiredLength} caracteres`;
    }
    if (this.formErrors[fieldName]) {
      return this.formErrors[fieldName][0];
    }
    return '';
  }

  getFullImageUrl(photoPath: string): string {
    if (!photoPath) return '';
    if (photoPath.startsWith('http')) return photoPath;
    return `http://127.0.0.1:8000/${photoPath}`;
  }

  getInitials(name: string, lastName = ''): string {
    const initials = (name.charAt(0) + lastName.charAt(0)).toUpperCase();
    return initials || 'U';
  }

  getRoleClass(role: string): string {
    switch (role) {
      case 'admin':
        return 'role-admin';
      case 'empleado':
        return 'role-empleado';
      case 'cliente':
        return 'role-cliente';
      default:
        return 'role-cliente';
    }
  }

  goToDashboard(): void {
    this.router.navigate(['/dashboard']);
  }

  // Métodos de paginación legacy (mantenidos por compatibilidad)
  nextPage(): void {
    this.changePage(this.currentPage + 1);
  }

  prevPage(): void {
    this.changePage(this.currentPage - 1);
  }
}
