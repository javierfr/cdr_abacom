import { Component } from '@angular/core';
import { SincronizarService } from '../services/sincronizar.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-sincronizar',
  standalone: true,
  imports: [CommonModule],  // Importamos HttpClientModule para standalone
  templateUrl: './sincronizar.component.html',
  styleUrls: ['./sincronizar.component.scss']
})
export default class SincronizarComponent {
  
  selectedFile: File = null;  // Para almacenar el archivo seleccionado

  constructor(private sincronizarService: SincronizarService) { }

  // Método que se ejecuta cuando se selecciona un archivo
  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];  // Captura el archivo CSV
  }  

  // Método para manejar la subida del archivo
  onUpload() {
    if (this.selectedFile) {
      this.sincronizarService.uploadExcel(this.selectedFile).subscribe(
        (response) => {
          console.log('Archivo subido con éxito', response);
        },
        (error) => {
          console.error('Error al subir el archivo:', error);
        }
      );
    } else {
      console.log('Por favor selecciona un archivo.');
    }
  }  
}
