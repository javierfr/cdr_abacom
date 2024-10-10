import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

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

    const headers = new HttpHeaders({
      'Accept': 'application/json',  // Asegurarse de que el servidor acepte JSON
    });

    return this.http.post(this.apiUrl, formData, { headers });
  }
}
