import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class JwtInterceptor implements HttpInterceptor {
    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        // add authorization header with jwt token if available

        const currentUser = localStorage.getItem('currentUser');
        if (currentUser) {
            const auth = btoa(`${currentUser}`);

            request = request.clone({
                setHeaders: {
                    Authorization: `Basic ${auth}`
                }
            });
        }

        return next.handle(request);
    }
}
