// login.component.ts
import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
})
export class LoginComponent {
  private authService = inject(AuthService);
  private router = inject(Router);

  email = '';
  password = '';
  loading = false;
  error = '';
  showPassword = false;

  // Validación de correo
  isEmailValid(): boolean {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(this.email);
  }

  onSubmit() {
    if (!this.email || !this.password) {
      this.error = 'Por favor completa todos los campos';
      return;
    }

    if (!this.isEmailValid()) {
      this.error = 'El correo ingresado no es válido';
      return;
    }

    this.loading = true;
    this.error = '';

    this.authService.login(this.email, this.password).subscribe({
      next: (response) => {
        this.loading = false;
        const user = response.data.user;
        if (user.role === 'admin' || user.role === 'empleado') {
          this.router.navigate(['/dashboard']);
        } else {
          this.router.navigate(['/']);
        }
      },
      error: (error) => {
        this.error = error.error?.message || 'Error al iniciar sesión';
        this.loading = false;
      },
    });
  }

  goToRegister() {
    this.router.navigate(['/register']);
  }
}
