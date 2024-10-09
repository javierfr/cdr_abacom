import { ComponentFixture, TestBed } from '@angular/core/testing';

import SincronizarComponent from './sincronizar.component';

describe('SincronizarComponent', () => {
  let component: SincronizarComponent;
  let fixture: ComponentFixture<SincronizarComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SincronizarComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SincronizarComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
