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
  showAlert: boolean = false; // Para mostrar o esconder la alerta
  alertMessage: string = '';  // Mensaje del alert
  alertType: string = 'danger'; // Tipo de alerta (success, danger, etc.)

  constructor(private sincronizarService: SincronizarService) { }

  // Método que se ejecuta cuando se selecciona un archivo
  onFileSelected(event: any) {
    this.selectedFile = event.target.files[0];  // Asegúrate de que se está capturando el archivo
    if (this.selectedFile) {
      this.showAlert = false; // Oculta la alerta si se seleccionó un archivo
      console.log('Archivo seleccionado:', this.selectedFile.name);  // Verifica que se captura el archivo
    } else {
      this.showAlert = true; // Muestra la alerta si no se seleccionó archivo
      this.alertMessage = 'Por favor selecciona un archivo';
    }
  }
  
  // Método para manejar la subida del archivo
  onUpload() {
    if (this.selectedFile) {
      this.sincronizarService.uploadExcel(this.selectedFile).subscribe(
        (response) => {
          this.alertMessage = 'Archivo subido con éxito';
          this.alertType = 'success'; // Cambia el tipo de alerta a success
          this.showAlert = true;
          this.hideAlertAfterTimeout(); // Ocultar después de cierto tiempo
          console.log('Archivo subido con éxito', response);
        },
        (error) => {
          this.alertMessage = 'Error al subir el archivo';
          this.alertType = 'danger'; // Tipo danger para errores
          this.showAlert = true;
          console.error('Error al subir el archivo:', error);
        }
      );
    } else {
      this.alertMessage = 'Por favor selecciona un archivo';
      this.alertType = 'danger'; // Tipo danger para errores
      this.showAlert = true;
    }
  }

  // Ocultar la alerta después de 3 segundos
  hideAlertAfterTimeout() {
    setTimeout(() => {
      this.showAlert = false;
    }, 3000); // Ocultar la alerta después de 3 segundos
  }

  // Método para cerrar la alerta manualmente
  closeAlert() {
    this.showAlert = false;
  }
}
