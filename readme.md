# 🛍️ Navy's - Sistema de Gestión de Tienda Online

Una aplicación web completa para la gestión de una tienda de vestidos de XV años y bautizos, desarrollada con **Angular 18** en el frontend y **Laravel** como API backend.

## 📋 Tabla de Contenidos

- [Características Principales](#características-principales)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Arquitectura del Sistema](#arquitectura-del-sistema)
- [Instalación y Configuración](#instalación-y-configuración)
- [Funcionalidades por Módulo](#funcionalidades-por-módulo)
- [Servicios y APIs](#servicios-y-apis)
- [Capturas de Pantalla](#capturas-de-pantalla)
- [Roles y Permisos](#roles-y-permisos)
- [Estructura del Proyecto](#estructura-del-proyecto)

## 🚀 Características Principales

### ✨ Para Clientes
- **Catálogo de productos** con filtros avanzados
- **Sistema de favoritos** personalizado
- **Carrito de compras** con persistencia
- **Proceso de checkout** completo
- **Seguimiento de pedidos** en tiempo real
- **Perfil de usuario** editable

### 🔧 Para Administradores
- **Panel de administración** completo
- **Gestión de productos** (CRUD completo)
- **Administración de usuarios** con roles
- **Gestión de pedidos** y estados
- **Dashboard con estadísticas**
- **Sistema de notificaciones**

## 🛠️ Tecnologías Utilizadas

### Frontend
- **Angular 18** - Framework principal
- **TypeScript** - Lenguaje de programación
- **Tailwind CSS** - Framework de estilos
- **RxJS** - Programación reactiva
- **EmailJS** - Envío de correos electrónicos

### Backend
- **Laravel** - Framework PHP
- **MySQL** - Base de datos
- **JWT** - Autenticación
- **Railway** - Hosting y despliegue

### Herramientas
- **Angular CLI** - Herramientas de desarrollo
- **Git** - Control de versiones
- **VS Code** - Editor de código

## 🏗️ Arquitectura del Sistema

```
┌─────────────────┐    HTTP/REST    ┌─────────────────┐
│   Angular App   │ ◄──────────────► │   Laravel API   │
│                 │                 │                 │
│  ┌───────────┐  │                 │  ┌───────────┐  │
│  │Components │  │                 │  │Controllers│  │
│  └───────────┘  │                 │  └───────────┘  │
│  ┌───────────┐  │                 │  ┌───────────┐  │
│  │ Services  │  │                 │  │  Models   │  │
│  └───────────┘  │                 │  └───────────┘  │
│  ┌───────────┐  │                 │  ┌───────────┐  │
│  │  Guards   │  │                 │  │Middleware │  │
│  └───────────┘  │                 │  └───────────┘  │
└─────────────────┘                 └─────────────────┘
```

## 📦 Instalación y Configuración

### Prerrequisitos
- Node.js (v18 o superior)
- Angular CLI (`npm install -g @angular/cli`)
- Git

### Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/YayisTorres/NAVYSangular
cd navys-store
```

2. **Instalar dependencias**
```bash
npm install
```

3. **Configurar variables de entorno**
```bash
# Crear archivo environment.ts
export const environment = {
  production: false,
  apiUrl: 'https://produccionnavyslaravel-production.up.railway.app/api'
};
```

4. **Ejecutar la aplicación**
```bash
ng serve
```

La aplicación estará disponible en `https://navys-a572a.web.app/`

## Sección: Diagrama Relacional

A continuación se muestra el diagrama relacional del sistema NAVYS, que representa las entidades y relaciones de una plataforma de e-commerce:

### Imagen del Diagrama Relacional

![Diagrama Relacional NAVYS](../mnt/data/NAVYS%20RELACIONAL.png)

---

## Análisis Relacional

### Tabla: USERS
- **Llave primaria:** `id`
- Relaciones:
  - Tiene muchos **favorites**
  - Tiene muchos **cart_items**
  - Tiene muchas **orders**
- Eliminación en cascada:
  - ✅ Al eliminar un usuario, también se eliminan:
    - sus **favoritos**
    - sus **carrito de compras**
    - sus **órdenes**
  - ❌ No se eliminan sus productos, ya que la relación entre `users` y `products` no está explícita. La tabla `products` no tiene `user_id`.

---

### Tabla: PRODUCTS
- **Llave primaria:** `id`
- Relaciones:
  - Es referenciada por:
    - **favorites**
    - **cart_items**
    - **order_items**
- Eliminación:
  - ❌ Si se elimina un producto, **no se indica** si se eliminan favoritos, carritos u órdenes relacionados.
  - Sería necesario implementar `ON DELETE CASCADE` para mantener integridad referencial.

---

### Tabla: FAVORITES
- Contiene:
  - `user_id` (FK)
  - `product_id` (FK)
- ✅ Se elimina cuando se borra el usuario (relación directa).
- ✅ Se elimina cuando se borra el producto, **si y solo si** hay ON DELETE CASCADE (no visible en el diagrama).

---

### Tabla: CART_ITEMS
- Contiene:
  - `user_id` (FK)
  - `product_id` (FK)
- ✅ Se borra al eliminar el usuario.
- ❌ No se indica que se borre si se elimina el producto.

---

### Tabla: ORDERS
- Contiene:
  - `user_id` (FK)
  - Datos extensos de envío, pago y estatus.
- ✅ Se elimina al borrar el usuario.

---

### Tabla: ORDER_ITEMS
- Contiene:
  - `order_id` (FK)
  - Datos redundantes del producto (nombre, precio, etc.)
- ✅ Se elimina al borrar la orden.

---

### Tabla: PERSONAL_ACCESS_TOKENS
- Tokens de autenticación vinculados a usuarios (indirectamente con `tokenable_id`)
- ❌ No se borra automáticamente si se elimina un usuario (no hay relación directa visible).

---

### Tabla: MIGRATIONS
- Tabla de control de versiones de migraciones de Laravel.
- No se relaciona con otras tablas.

---

## Conclusiones
- ✅ Al borrar un usuario, sí se eliminan:
  - Sus favoritos
  - Sus carritos
  - Sus órdenes y los ítems relacionados
- ❌ No se eliminan productos que podrían haber sido subidos por el usuario (la tabla `products` no tiene `user_id`)

## 🎯 Funcionalidades por Módulo

### 🔐 Autenticación
- **Login/Registro** con validación
- **Recuperación de contraseña**
- **Gestión de sesiones** con JWT
- **Roles de usuario** (Admin, Empleado, Cliente)

### 🏠 Página Principal
- **Carrusel de imágenes** promocionales
- **Productos destacados**
- **Navegación por categorías**
- **Búsqueda rápida**

### 🛍️ Catálogo de Productos
- **Vista de cuadrícula/lista**
- **Filtros por categoría, precio, talla**
- **Ordenamiento** (precio, nombre, fecha)
- **Paginación** optimizada

### ❤️ Sistema de Favoritos
- **Agregar/quitar favoritos**
- **Lista de productos favoritos**
- **Sincronización** entre dispositivos

### 🛒 Carrito de Compras
- **Agregar productos** con variantes
- **Modificar cantidades**
- **Cálculo automático** de totales
- **Persistencia** de datos

### 💳 Proceso de Pago
- **Formulario de envío**
- **Métodos de pago** múltiples
- **Confirmación de pedido**
- **Envío de email** automático

### 📦 Gestión de Pedidos
- **Historial de pedidos**
- **Estados de seguimiento**
- **Detalles completos**
- **Cancelación** de pedidos

### 👥 Panel de Administración
- **Dashboard** con métricas
- **Gestión de usuarios**
- **Administración de productos**
- **Control de pedidos**

## 🔧 Servicios y APIs

### AuthService
```typescript
// Autenticación y autorización
login(email: string, password: string): Observable<LoginResponse>
register(userData: RegisterData): Observable<any>
logout(): Observable<any>
getCurrentUser(): Observable<any>
isAuthenticated(): boolean
isAdmin(): boolean
```

### ProductService
```typescript
// Gestión de productos
getAllProducts(filters?: any): Observable<any>
getProductById(id: string): Observable<any>
createProduct(productData: Partial<Product>): Observable<any>
updateProduct(id: string, productData: Partial<Product>): Observable<any>
deleteProduct(id: string): Observable<any>
```

### CartService
```typescript
// Carrito de compras
getCart(): Observable<CartResponse>
addToCart(item: AddToCartRequest): Observable<any>
updateCartItem(itemId: number, quantity: number): Observable<any>
removeFromCart(itemId: number): Observable<any>
clearCart(): Observable<any>
```

### OrderService
```typescript
// Gestión de pedidos
getOrders(page?: number, status?: string): Observable<OrdersResponse>
getOrder(id: string): Observable<OrderResponse>
createOrder(orderData: CreateOrderRequest): Observable<OrderResponse>
cancelOrder(id: string): Observable<OrderResponse>
```

### FavoriteService
```typescript
// Sistema de favoritos
getFavorites(): Observable<FavoriteResponse>
addToFavorites(productId: string): Observable<any>
removeFromFavorites(productId: string): Observable<any>
toggleFavorite(productId: string): Observable<any>
```

## 📸 Capturas de Pantalla

### 🔐 Página de Login
![Login Page](https://via.placeholder.com/800x600/f3f4f6/374151?text=Login+Page)

*Página de inicio de sesión con validación de formularios y diseño responsive*

### 🏠 Página Principal
![Home Page](https://via.placeholder.com/800x600/f3f4f6/374151?text=Home+Page)

*Página principal con carrusel de imágenes y productos destacados*

### ❤️ Página de Favoritos
![Favorites Page](https://via.placeholder.com/800x600/f3f4f6/374151?text=Favorites+Page)

*Lista de productos favoritos del usuario con opciones de gestión*

### 📦 Mis Pedidos
![Orders Page](https://via.placeholder.com/800x600/f3f4f6/374151?text=My+Orders)

*Historial completo de pedidos con estados y detalles*

### 🛒 Página de Compra
![Shopping Page](https://via.placeholder.com/800x600/f3f4f6/374151?text=Shopping+Cart)

*Carrito de compras con productos seleccionados y cálculos*

### 💳 Página de Pago
![Payment Page](https://via.placeholder.com/800x600/f3f4f6/374151?text=Payment+Page)

*Formulario de checkout con información de envío y pago*

### 🎠 Carrusel de Productos
![Product Carousel](https://via.placeholder.com/800x600/f3f4f6/374151?text=Product+Carousel)

*Carrusel interactivo de productos con navegación automática*

### 🎛️ Panel Administrador
![Admin Dashboard](https://via.placeholder.com/800x600/f3f4f6/374151?text=Admin+Dashboard)

*Dashboard administrativo con estadísticas y accesos rápidos*

### 👥 Administrar Usuarios
![User Management](https://via.placeholder.com/800x600/f3f4f6/374151?text=User+Management)

*Panel de gestión de usuarios con filtros y paginación avanzada*

### 📦 Administrar Productos
![Product Management](https://via.placeholder.com/800x600/f3f4f6/374151?text=Product+Management)

*Sistema completo de gestión de productos con CRUD*

## 👤 Roles y Permisos

### 🔴 Administrador
- **Acceso completo** al sistema
- **Gestión de usuarios** y roles
- **Administración de productos**
- **Control total de pedidos**
- **Acceso a estadísticas**

### 🟡 Empleado
- **Gestión de productos** (limitada)
- **Administración de pedidos**
- **Atención al cliente**
- **Reportes básicos**

### 🟢 Cliente
- **Navegación del catálogo**
- **Gestión de favoritos**
- **Carrito y compras**
- **Seguimiento de pedidos**
- **Perfil personal**

## 📁 Estructura del Proyecto

```
src/
├── app/
│   ├── components/
│   │   ├── auth/
│   │   │   ├── login/
│   │   │   └── register/
│   │   ├── home/
│   │   ├── products/
│   │   ├── cart/
│   │   ├── orders/
│   │   ├── favorites/
│   │   ├── admin/
│   │   │   ├── dashboard/
│   │   │   ├── users/
│   │   │   └── products/
│   │   └── shared/
│   │       ├── header/
│   │       ├── footer/
│   │       ├── product-card/
│   │       └── sidebar-menu/
│   ├── services/
│   │   ├── auth.service.ts
│   │   ├── product.service.ts
│   │   ├── cart.service.ts
│   │   ├── order.service.ts
│   │   ├── favorite.service.ts
│   │   ├── user.service.ts
│   │   └── email.service.ts
│   ├── guards/
│   │   ├── auth.guard.ts
│   │   └── admin.guard.ts
│   ├── interfaces/
│   └── utils/
├── assets/
├── environments/
└── styles/
```

## 🔄 Estados de Pedidos

| Estado | Descripción | Acciones Disponibles |
|--------|-------------|---------------------|
| `pending` | Pedido creado, esperando confirmación | Confirmar, Cancelar |
| `confirmed` | Pedido confirmado por el admin | Procesar, Cancelar |
| `processing` | Pedido en preparación | Enviar |
| `shipped` | Pedido enviado al cliente | Marcar como entregado |
| `delivered` | Pedido entregado exitosamente | - |
| `cancelled` | Pedido cancelado | - |

## 💳 Métodos de Pago
(simulado)
- **💵 Efectivo** - Pago contra entrega
- **🏦 Transferencia** - Transferencia bancaria
- **💳 Tarjeta** - Tarjeta de crédito/débito
- **🌐 PayPal** - Pago en línea (próximamente)

## 📧 Sistema de Notificaciones

### EmailJS Integration
- **Confirmación de pedidos** automática
- **Actualizaciones de estado** de pedidos
- **Notificaciones de envío**
- **Recordatorios** personalizados

## 🔒 Seguridad

- **Autenticación JWT** con tokens seguros
- **Validación de formularios** en frontend y backend
- **Sanitización de datos** de entrada
- **Protección CORS** configurada
- **Headers de seguridad** implementados

## 🚀 Despliegue

### Frontend (Vercel/Netlify)
```bash
ng build --prod
# Subir dist/ a tu plataforma de hosting
```

### Backend (Railway/Heroku)
- API Laravel desplegada en Railway
- Base de datos MySQL en la nube
- Variables de entorno configuradas

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 👨‍💻 Autor

**Tu Nombre**
- Nombre: Guadalupe Dayanira Torres Quiroz
- Email: torresquirozdayanira@gmail.com
