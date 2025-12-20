Feature: Penghuni Kos

  Scenario: Penghuni kos melihat daftar kos yang tersedia
    When penghuni kos membuka aplikasi
    And penghuni kos melakukan login
    Then halaman utama ditampilkan
    And data-data kos yang tersedia ditampilkan lengkap dengan informasi kos

  Scenario: Penghuni kos melihat daftar kamar kos yang tersedia
    When penghuni kos membuka aplikasi
    And penghuni kos melakukan login
    Then halaman utama ditampilkan
    When penghuni kos menekan salah satu card kos
    Then halaman kamar kos ditampilkan
    And data-data kamar kos yang tersedia ditampilkan sesuai dengan ketersediaan kamar-nya

  Scenario: Penghuni kos melakukan pemesanan And pembayaran kamar
    When penghuni kos membuka aplikasi
    And penghuni kos melakukan login
    Then halaman utama ditampilkan
    When penghuni kos menekan salah satu card kos
    Then halaman kamar kos ditampilkan
    When penghuni kos menekan tombol "Pesan"
    Then halaman konfirmasi pembayaran ditampilkan
    When penghuni kos menekan tombol "Bayar"
    Then halaman pemilihan metode pembayaran ditampilkan
    When penghuni kos memilih "Qris" sebagai metode pembayaran
    Then QR Code pembayaran ditampilkan
    When penghuni melakukan pembayaran
    Then aplikasi menampilkan pemberitahuan bahwa pembayaran berhasil
    And penghuni kos berhasil melakukan pemesanan And pembayaran
    And halaman akan menuju ke halaman riwayat pemesanan
    And data pesanan akan dikirimkan ke pemilik kos

  Scenario: Penghuni kos melihat riwayat pemesanan kamar
    When penghuni kos membuka aplikasi
    And penghuni kos melakukan login
    Then halaman utama ditampilkan
    When penghuni kos menekan menu "Pemesanan" pada navigation bar
    Then halaman pemesanan menampilkan data-data pesanan yang dilakukan oleh user yang seAndg login beserta status-nya

  Scenario: Penghuni kos memberikan review pada suatu kos
    When penghuni kos membuka aplikasi
    And penghuni kos melakukan login
    Then halaman utama ditampilkan
    When penghuni kos menekan menu "Pemesanan" pada navigation bar
    Then halaman akan menampilkan data-data kos yang terdapat tombol action "icon bintang"
    When penghuni kos menekan tombol review
    And penghuni kos mengisi review untuk kos yang dipilih
    And penghuni kos menekan tombol "Submit Review"
    Then data review akan ditampilkan pada halaman kos terkait