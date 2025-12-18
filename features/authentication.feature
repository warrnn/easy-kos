Feature: Autentikasi User

  Scenario: User membuat akun baru And berhasil registrasi akun
    Given user membuka aplikasi
    And user pergi ke halaman register
    And user mengisi form register
    And user menekan tombol register
    Then user berhasil registrasi akun baru
    And data akun user tersimpan di database
    And user ter-direct ke halaman login

  Scenario: User dengan otoritas akun admin melakukan login untuk masuk ke dashboard admin
    When admin membuka aplikasi
    And admin mengisi form login
    And admin menekan tombol login
    Then admin berhasil masuk ke dashboard admin

  Scenario: User dengan otoritas akun pemilik kos melakukan login untuk masuk ke dashboard pemilik kos
    When pemilik kos membuka aplikasi
    And pemilik kos mengisi form login
    And pemilik kos menekan tombol login
    Then pemilik kos berhasil masuk ke dashboard pemilik kos

  Scenario: User dengan otoritas akun penghuni kos melakukan login untuk masuk ke halaman utama
    When penghuni kos membuka aplikasi
    And penghuni kos mengisi form login
    And penghuni kos menekan tombol login
    Then penghuni kos berhasil masuk ke halaman utama

  Scenario: User login dengan mengisi kredensial yang salah
    When user membuka aplikasi
    And user mengisi form login dengan kredensial salah
    And user menekan tombol login
    Then aplikasi menolak login dari user
    And user tetap di halaman login

