import { Component, OnInit } from '@angular/core';
import { LoginService } from '../services/login.service';
import { TranslateService } from '../translate.service';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

<<<<<<< Updated upstream
  constructor(private loginService: LoginService, private translate: TranslateService) { }
=======
  constructor(private loginService: LoginService) { }
>>>>>>> Stashed changes

  ngOnInit() {
  }

  userLoged() {
    return this.loginService.userLoged();
  }

  logout() {
    this.loginService.unsetLocalStorage();
  }

  setLang(lang: string) {
    this.translate.use(lang);
  }

}
