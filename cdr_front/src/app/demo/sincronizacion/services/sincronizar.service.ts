import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { catchError, Observable, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'  // Declaramos el servicio a nivel de aplicación
})
export class SincronizarService {

  // private apiUrl = 'http://localhost:8080/api/sincronizar/';  // URL del endpoint de CodeIgniter
  private apiUrl = 'https://cdr.abacom.mx/api/sincronizar/';  // URL del endpoint de CodeIgniter

  constructor(private http: HttpClient) { }

  // Método para subir el archivo Excel
  uploadExcel(file: File): Observable<any> {
    const headers = new HttpHeaders({
      'enctype': 'multipart/form-data'
    });

    
    const formData: FormData = new FormData();
    formData.append('file', file);  // Añadimos el archivo al FormData
  
    return this.http.post(this.apiUrl+'uploadExcel', formData, { headers }).pipe(
      catchError((error: HttpErrorResponse) => {
        let errorMsg = 'Error desconocido al subir el archivo';
        if (error.error instanceof ErrorEvent) {
          // Error del cliente
          errorMsg = `Error del cliente: ${error.error.message}`;
        } else {
          // Error del servidor
          errorMsg = `Error del servidor: ${error.status} - ${error.statusText}`;
        }
        return throwError(() => new Error(errorMsg));
      })
    );
  }
 
  // Método para sincronizar con Zoho
  sincronizarZoho(): Observable<any> {
    return this.http.post(this.apiUrl + 'zoho', {})
      .pipe(
        catchError((error: HttpErrorResponse) => {
          let errorMsg = 'Error desconocido al sincronizar con Zoho';
          if (error.error instanceof ErrorEvent) {
            // Error del cliente
            errorMsg = `Error del cliente: ${error.error.message}`;
          } else {
            // Error del servidor
            errorMsg = `Error del servidor: ${error.status} - ${error.statusText}`;
          }
          return throwError(() => new Error(errorMsg));
        })
      );
  }
}