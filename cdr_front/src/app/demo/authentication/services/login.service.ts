import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { catchError, map, Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class LoginService {

  private apiUrl = 'https://example.com/api/auth'; // URL de la API

  constructor(private http: HttpClient) { }

  // Método para manejar el inicio de sesión
  login(email: string, password: string): Observable<any> {
    const headers = new HttpHeaders({ 'Content-Type': 'application/json' });
    const body = { email, password };

    return this.http
      .post(`${this.apiUrl}/login`, body, { headers })
      .pipe(
        map((response: any) => {
          // Manejar la respuesta del servidor (guardar token, etc.)
          if (response.token) {
            localStorage.setItem('authToken', response.token); // Almacenar el token
          }
          return response;
        }),
        catchError((error) => {
          // Manejo de errores
          console.error('Error de autenticación:', error);
          throw error;
        })
      );
  }

  // Método para cerrar sesión
  logout(): void {
    localStorage.removeItem('authToken');
  }

  // Verificar si el usuario está autenticado
  isAuthenticated(): boolean {
    return !!localStorage.getItem('authToken');
  }
}
