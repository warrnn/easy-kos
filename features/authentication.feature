Feature: Autentikasi akun
  Sebagai user

  Background:
    Given user membuka aplikasi

  Scenario: User melakukan registrasi
    And user pergi ke halaman register
    When user mengisi form register, tipe akun "penghuni", username "andre", password "andre123"
    And user menekan tombol register
    Then user berhasil terdaftar