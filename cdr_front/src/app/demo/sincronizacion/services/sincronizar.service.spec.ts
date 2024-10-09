import { TestBed } from '@angular/core/testing';

import { SincronizarService } from './sincronizar.service';

describe('SincronizarService', () => {
  let service: SincronizarService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(SincronizarService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
