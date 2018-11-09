import { ActivatedRouteSnapshot, Router } from '@angular/router';
import { Component, OnInit } from '@angular/core';
import { LoginService } from '../services/login.service';

@Component({
  selector: 'app-sign-in',
  templateUrl: './sign-in.component.html',
  styleUrls: ['./sign-in.component.css']
})
export class SignInComponent implements OnInit {

  modelForm: any = {
    login: '',
    password: '',
  };

  constructor(private loginService: LoginService, private router: Router) { }

  ngOnInit() {
  }

  loginUser() {
    console.log(this.modelForm);
    this.loginService.login(this.modelForm).subscribe( res => {

      this.router.navigate(['/dashboard']);
    }, error => {

      this.loginService.unsetLocalStorage();
    });
  }

}
