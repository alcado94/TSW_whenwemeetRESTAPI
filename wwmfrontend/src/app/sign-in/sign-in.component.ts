import { Router, ActivatedRoute } from '@angular/router';
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
  show = true;
  returnUrl: string;

  constructor(private route: ActivatedRoute,
    private loginService: LoginService, private router: Router, private fb: FormBuilder) { }

  ngOnInit() {

    this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';

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
      if (this.returnUrl.length > 4) {
        this.router.navigate([this.returnUrl]);
      } else {
        this.router.navigate(['/dashboard']);
      }
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
