import { AuthGuard } from './helpers/auth.guard';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HeaderComponent } from './header/header.component';
import { LoginComponent } from './login/login.component';
import { SignInComponent } from './sign-in/sign-in.component';
import { SignUpComponent } from './sign-up/sign-up.component';
import { FormsModule } from '@angular/forms';
import { JwtInterceptor } from './helpers/jwt.interceptor';
import { LoginService } from './services/login.service';
import { DashboardComponent } from './dashboard/dashboard.component';
import { LoginShowCurrentDirective } from './helpers/directives/login-show-current.directive';

import { ReactiveFormsModule } from '@angular/forms';
import { CardComponent } from './dashboard/card/card.component';
import { PollDetailComponent } from './poll-detail/poll-detail.component';


@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    LoginComponent,
    SignInComponent,
    SignUpComponent,
    DashboardComponent,
    LoginShowCurrentDirective,
    CardComponent,
    PollDetailComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule
  ],
  providers: [
    AuthGuard,
    LoginService,
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true },
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
