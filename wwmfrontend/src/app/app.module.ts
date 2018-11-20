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
import { CheckHourStateDirective } from './helpers/directives/check-hour-state.directive';
import { ShowHourStartPipe } from './helpers/pipes/show-hour-start.pipe';
import { ShowDatePipe } from './helpers/pipes/show-date.pipe';
import { PollParticipateComponent } from './poll-participate/poll-participate.component';
import { AddpollComponent } from './addpoll/addpoll.component';
import { DayBoxComponent } from './addpoll/day-box/day-box.component';
import { HourBoxComponent } from './addpoll/hour-box/hour-box.component';
import { EditpollComponent } from './editpoll/editpoll.component';



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
    PollDetailComponent,
    CheckHourStateDirective,
    ShowHourStartPipe,
    ShowDatePipe,
    PollParticipateComponent,
    AddpollComponent,
    DayBoxComponent,
    HourBoxComponent,
    EditpollComponent
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
  entryComponents: [
    DayBoxComponent,
    HourBoxComponent
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
