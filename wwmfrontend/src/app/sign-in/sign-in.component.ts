import { ActivatedRouteSnapshot, Router } from '@angular/router';
import { Component, OnInit } from '@angular/core';
import { LoginService } from '../services/login.service';
import { FormGroup, FormBuilder, Validators, FormControl } from '@angular/forms';

@Component({
  selector: 'app-sign-in',
  templateUrl: './sign-in.component.html',
  styleUrls: ['./sign-in.component.css']
})
export class SignInComponent implements OnInit {

  myForm: FormGroup;
  loginCtrl: FormControl;
  passwordCtrl: FormControl;
  show = false;

  constructor(private loginService: LoginService, private router: Router, private fb: FormBuilder) { }

  ngOnInit() {
    this.loginCtrl = new FormControl('', Validators.required);
    this.passwordCtrl = new FormControl('', Validators.required);

    this.myForm = this.fb.group({
      login: this.loginCtrl,
      password: this.passwordCtrl
    });
  }

  submitHandler() {

    const formValue = this.myForm.value;

    this.loginService.login(formValue).subscribe(res => {
      this.router.navigate(['/dashboard']);
    }, error => {
      this.loginService.unsetLocalStorage();
    });
  }

  showPass() {
    if (this.show) {
      this.show = false;
    } else {
      this.show = true;
    }
  }
}
