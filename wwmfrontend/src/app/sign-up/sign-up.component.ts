import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { LoginService } from '../services/login.service';
import { Router } from '@angular/router';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-sign-up',
  templateUrl: './sign-up.component.html',
  styleUrls: ['./sign-up.component.css']
})
export class SignUpComponent implements OnInit {

  myForm: FormGroup;
  loginCtrl: FormControl;
  passwordCtrl: FormControl;
  nameCtrl: FormControl;
  surnameCtrl: FormControl;
  emailCtrl: FormControl;
  imgCtrl: FormControl;

  @ViewChild('fileInput') fileInput: ElementRef;

  constructor(private loginService: LoginService, private router: Router, private fb: FormBuilder) { }

  ngOnInit() {
    this.loginCtrl = new FormControl('', Validators.required);
    this.passwordCtrl = new FormControl('', Validators.required);
    this.nameCtrl = new FormControl('', Validators.required);
    this.surnameCtrl = new FormControl('', Validators.required);
    this.imgCtrl = new FormControl('', Validators.required);
    this.emailCtrl = new FormControl('', Validators.required);

    this.myForm = this.fb.group({
      login: this.loginCtrl,
      passwd: this.passwordCtrl,
      name: this.nameCtrl,
      surname: this.surnameCtrl,
      img: this.imgCtrl,
      email: this.emailCtrl
    });
  }

  onFileChange(event) {
    if (event.target.files.length > 0) {
      const file = event.target.files[0];
      this.myForm.get('img').setValue(file);
    }
  }


  private prepareSave(): any {
    const input = new FormData();
    input.append('name', this.myForm.get('name').value);
    input.append('surname', this.myForm.get('surname').value);
    input.append('password', this.myForm.get('passwd').value);
    input.append('login', this.myForm.get('login').value);
    input.append('img', this.myForm.get('img').value);
    input.append('email', this.myForm.get('email').value);

    return input;
  }

  submitHandler() {

    const formValue = this.prepareSave();

    this.loginService.singUp(formValue).subscribe(res => {
      this.router.navigate(['/index/signin']);
    }, error => {
      console.log(error);
    });
  }

}
