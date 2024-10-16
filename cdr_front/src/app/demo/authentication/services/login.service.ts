import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, map, Observable, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class LoginService {

  private apiUrl = 'http://localhost:8080/api/auth/';
  router: any;

  constructor(private http: HttpClient) { }

  login(email: string, password: string): Observable<any> {
    const headers = new HttpHeaders({ 'Content-Type': 'application/json' });
    const body = { email, password };

    return this.http
      .post(`${this.apiUrl}login`, body, { headers })
      .pipe(
        map((response: any) => {
          // Verificar el status de la respuesta
          if (response.status === 200 && response.token) {
            console.log('Login successful:', response);
            localStorage.setItem('authToken', response.token); // Almacenar el token
            return response;
          } else {
            console.error('Login failed:', response);
            // Si el status no es 200, lanzar un error
            throw new Error('Login failed: Invalid response from server');
          }
        }),
        catchError((error) => {
          // Manejo de errores
          console.error('Error de autenticación:', error);
          return throwError(() => new Error('Error de autenticación, por favor intenta nuevamente.'));
        })
      );
  }

  // logout(): void {
  //   localStorage.removeItem('authToken');
  // }
  // logout(): void {
  //   localStorage.removeItem('authToken');
  //   this.router.navigate(['/login']); // Redirige al login tras cerrar sesión
  // }

  isAuthenticated(): boolean {
    return !!localStorage.getItem('authToken');
  }
}
