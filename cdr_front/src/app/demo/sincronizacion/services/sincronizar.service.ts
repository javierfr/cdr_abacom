import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { catchError, Observable, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'  // Declaramos el servicio a nivel de aplicación
})
export class SincronizarService {

  private apiUrl = 'https://cdr.abacom.mx/api/sincronizar/uploadExcel';  // URL del endpoint de CodeIgniter

  constructor(private http: HttpClient) { }

  // Método para subir el archivo Excel
  uploadExcel(file: File): Observable<any> {
    const formData: FormData = new FormData();
    formData.append('file', file);  // Añadimos el archivo al FormData
  
    return this.http.post(this.apiUrl, formData).pipe(
      catchError((error: HttpErrorResponse) => {
        // Aquí podemos capturar el error HTTP y devolver un mensaje de error
        console.error('Error al subir el archivo:', error);
  
        // Si es un error de respuesta, podemos devolver el mensaje adecuado
        if (error.error instanceof ErrorEvent) {
          // Error del lado del cliente
          return throwError(() => new Error('Error en la subida del archivo. Intenta nuevamente.'));
        } else {
          // Error del lado del servidor
          return throwError(() => new Error(`Error del servidor: ${error.status} ${error.statusText}`));
        }
      })
    );
  }
  
}
