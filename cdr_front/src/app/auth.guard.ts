// import { CanActivateFn } from '@angular/router';

// export const authGuard: CanActivateFn = (route, state) => {
//   return true;
// };
import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { LoginService } from './demo/authentication/services/login.service';
// import { LoginService } from './services/login.service'; // Importa tu servicio de login

@Injectable({
  providedIn: 'root',
})
export class AuthGuard implements CanActivate {
  constructor(private loginService: LoginService, private router: Router) {}

  canActivate(): boolean {
    if (this.loginService.isAuthenticated()) {
      return true; // El usuario está autenticado, permite el acceso
    } else {
      this.router.navigate(['/login']); // Redirige al login si no está autenticado
      return false; // Bloquea el acceso
    }
  }
}
