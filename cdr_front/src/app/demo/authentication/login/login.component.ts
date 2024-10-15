// angular import
import { Component } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { LoginService } from '../services/login.service';
import { FormsModule } from '@angular/forms'; // Importar FormsModule
import { CommonModule } from '@angular/common'; // Importar CommonModule

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [RouterModule, FormsModule, CommonModule], // Añadir CommonModule a las importaciones
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export default class LoginComponent {
  email = '';
  password = '';
  // public method
  SignInOptions = [
    {
      image: 'assets/images/authentication/google.svg',
      name: 'Google'
    },
    {
      image: 'assets/images/authentication/twitter.svg',
      name: 'Twitter'
    },
    {
      image: 'assets/images/authentication/facebook.svg',
      name: 'Facebook'
    }
  ];

  constructor(private loginService: LoginService, private router: Router) {}

  // Método para manejar el evento de login
  login(): void {
    this.loginService.login(this.email, this.password).subscribe({
      next: (response) => {
        // Redirigir al dashboard o alguna otra página en caso de éxito
        this.router.navigate(['/dashboard']);
      },
      error: (error) => {
        // Mostrar un mensaje de error al usuario
        alert('Error al iniciar sesión. Por favor, intenta nuevamente.');
      }
    });
  }
}
