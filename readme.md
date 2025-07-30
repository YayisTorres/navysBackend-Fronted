# 🛍️ Navy's - Sistema de Gestión de Tienda Online

## 1. 📌 Encabezado

- **Nombre del Proyecto:** Navy's - Sistema de Gestión de Tienda Online  
- **Número de equipo:** 13
- **Integrantes:**  
  - Guadalupe Dayanira Torres Quiroz (Front-end)
  - Maria de Lourdes Hernaández Hernández(Back-end)

- **¿Qué hace el sistema?**  
  Es un sistema web que permite gestionar una tienda de vestidos de XV años y bautizos.  
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
- **Tipo de sistema:** Sistema Web
- ## Análisis Relacional

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

### 🔐 Página de Login (logueo)
![Login error](https://github.com/YayisTorres/navysBackend-Fronted/blob/main/frontend/public/img/loginerror.png)
![Login correcto](https://github.com/YayisTorres/navysBackend-Fronted/blob/main/frontend/public/img/login.png)

*Página de inicio de sesión con validación de formularios y diseño responsive*

### 🏠 Niveles Usuarios 
![Pagina Usuario](https://github.com/YayisTorres/navysBackend-Fronted/blob/main/frontend/public/img/carrusel.png)
![Pagina Administrador](https://github.com/YayisTorres/navysBackend-Fronted/blob/main/frontend/public/img/dashboartprincipal.png)
*Dashboard administrativo con estadísticas y accesos rápidos*
### 👥 Administrar Usuarios
![User Management](https://github.com/YayisTorres/NAVYSangular/blob/main/public/img/administrador-usuarios.png)

*Panel de gestión de usuarios con filtros y paginación *

### 📦 Administrar Productos
![Product Management](https://github.com/YayisTorres/NAVYSangular/blob/main/public/img/gestor-productos.png)

### Procesos Principales 

### 📦 Mis Pedidos
![Orders Page](https://github.com/YayisTorres/NAVYSangular/blob/main/public/img/pedidos.png)

*Historial completo de pedidos con estados y detalles*

### 🛒 Página de Compra
![Shopping Page](https://github.com/YayisTorres/NAVYSangular/blob/main/public/img/carrito.png)

*Carrito de compras con productos seleccionados y cálculos*

### 💳 Página de Pago
![Payment Page](https://github.com/YayisTorres/NAVYSangular/blob/main/public/img/pagos.png)

*Formulario de checkout con información de envío y pago*

### 🎛️ Envio de Correos 
![Envio de Correo](https://github.com/YayisTorres/navysBackend-Fronted/blob/main/frontend/public/img/enviocorreo.png)
![Correo Recibido](https://github.com/YayisTorres/navysBackend-Fronted/blob/main/frontend/public/img/correorecibido.png)
*Se envia un correo para confirmar al usuario que su pedido se a registrado correctamente*

### Otros

###  ❤️ Favoritos
![Pagina Favoritos](https://github.com/YayisTorres/navysBackend-Fronted/blob/main/frontend/public/img/favoritos.png)

*Muestra la pagina de favoritos*

### 🎠 Sugerencias Productos
![sugerencias](https://github.com/YayisTorres/navysBackend-Fronted/blob/main/frontend/public/img/sugerencias.png)

*Seleccionas un producto y te da sugerencias de productos relacionados con el producto seleccionado *


## 🛠️ Tecnologías Utilizadas

### Frontend
- *Angular 18* - Framework principal
- *TypeScript* - Lenguaje de programación
- *Tailwind CSS* - Framework de estilos
- *RxJS* - Programación reactiva
- *EmailJS* - Envío de correos electrónicos

### Backend
- *Laravel* - Framework PHP
- *MySQL* - Base de datos
- *JWT* - Autenticación
- *Railway* - Hosting y despliegue







