BRD - Sistem Informasi Manajemen BUMDes 

# **BUSINESS REQUIREMENT DOCUMENT (BRD)** 

**Sistem Informasi Manajemen BUMDes Terintegrasi** 

_Studi Kasus: Kecamatan Minggir - Fase Percobaan (Kelurahan Sendangsari & Sendangrejo)_ 

Versi 2.0 16 September 2026 

_Status: Draft untuk Diskusi Awal (Revisi 2)_ 

Halaman 1 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **1. Informasi Dokumen** 

|**Item**|**Keterangan**|
|---|---|
|Nama Proyek|Sistem Informasi Manajemen BUMDes Terintegrasi (SIM-BUMDes)|
|Versi Dokumen|2.0 (revisi setelah diskusi awal)|
|Tanggal|16 September 2026|
|Status|Draft - untuk diskusi awal dengan dosen pembimbing riset|
|Disusun oleh|Tim Pengembang (Software Engineer)|
|Cakupan Fase|Fase 1 (Percobaan): Kelurahan Sendangsari & Sendangrejo, Kecamatan Minggir|



## **2. Latar Belakang** 

Badan Usaha Milik Desa (BUMDes) merupakan lembaga usaha yang dikelola oleh desa dan masyarakat dalam rangka memperkuat perekonomian desa, dibentuk berdasarkan kebutuhan dan potensi desa. Pengelolaan BUMDes saat ini pada banyak kelurahan masih dilakukan secara manual atau semi-manual, sehingga menyulitkan pemantauan kinerja, transparansi keuangan, dan koordinasi antar-BUMDes dalam satu kecamatan. 

Kecamatan Minggir memiliki 5 kelurahan (Sendangagung, Sendangarum, Sendangmulyo, Sendangrejo, Sendangsari) yang masing-masing memiliki atau berpotensi memiliki BUMDes mandiri, dengan Sendangsari berperan sebagai koordinator pusat kecamatan. Setiap BUMDes menjalankan beberapa unit usaha (PAMDes, peternakan, mitra tani/jual-beli, penyewaan mobil, dan pengelolaan sampah) yang masingmasing memiliki karakteristik pencatatan keuangan berbeda. 

Untuk mendukung tata kelola yang lebih baik dan sejalan dengan enam prinsip pengelolaan BUMDes (kooperatif, partisipatif, emansipatif, transparan, akuntabel, dan sustainable), dibutuhkan sebuah sistem informasi yang mampu mengelola struktur berjenjang antar-wilayah, mencatat transaksi tiap unit usaha secara akurat, mengelola iuran dan mekanisme referral antar-BUMDes, serta menyediakan pemantauan operasional secara real-time bagi pengawas, penasihat, dan direktur. 

## **3. Tujuan** 

1. Menyediakan sistem pencatatan keuangan (neraca sederhana: input, output, untung-rugi) untuk setiap unit usaha BUMDes secara terpisah dan sesuai karakteristik masing-masing unit. 

2. Menyediakan struktur data berjenjang (provinsi > kota > kecamatan > kelurahan) yang memungkinkan penambahan BUMDes baru secara mandiri oleh admin/super admin. 

3. Mengelola mekanisme iuran bulanan antar-BUMDes ke koordinator kecamatan beserta fitur referral untuk mendorong pertumbuhan jaringan BUMDes. 

4. Menyediakan akses berjenjang (super admin, pengawas, penasihat, direktur, admin BUMDes, sekretaris, bendahara, admin unit, dan pengguna/pelanggan) sesuai kebutuhan dan kewenangan masing-masing, termasuk kemampuan mengaktifkan/menonaktifkan BUMDes, unit, dan pelanggan. 

5. Menyediakan dashboard monitoring operasional beserta fitur pemberian catatan/feedback dan notifikasi real-time agar arahan pengawas/penasihat/direktur/super admin cepat ditindaklanjuti pengurus BUMDes. 

Halaman 2 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

6. Menghasilkan laporan keuangan per unit dan per BUMDes yang dapat diekspor ke Excel dengan format yang rapi. 

## **4. Ruang Lingkup** 

- **4.1 Dalam Lingkup (In-Scope) - Fase 1** 

- Manajemen struktur wilayah: provinsi, kota, kecamatan, kelurahan, dengan penerapan uji coba pada 2 kelurahan (Sendangsari dan Sendangrejo) di Kecamatan Minggir, masing-masing entitas memiliki ID unik. 

- Manajemen data BUMDes dan 5 unit usaha standar (PAMDes, peternakan, mitra tani, penyewaan mobil, sampah) dengan kemampuan menambah unit baru, serta mengaktifkan/menonaktifkan BUMDes, unit, dan pelanggan sesuai kewenangan masing-masing role. 

- Modul pencatatan transaksi (input-output, untung-rugi) yang dapat disesuaikan skemanya per jenis unit. 

- Modul tagihan pengguna per unit dengan pencatatan manual oleh admin unit dan opsi upload bukti transfer untuk diverifikasi. 

- Modul iuran bulanan BUMDes ke koordinator kecamatan beserta modul kas dan referral antarBUMDes (masa berlaku kode 5 hari, batas verifikasi aktivitas 15 hari). 

- Manajemen akun dan hak akses berjenjang (super admin, pengawas, penasihat, direktur, admin BUMDes, sekretaris, bendahara, admin unit, pengguna). 

- Dashboard monitoring operasional dan kesehatan BUMDes secara real-time, dilengkapi fitur catatan/feedback dari pengawas/penasihat/direktur/super admin ke BUMDes/unit beserta notifikasi real-time. 

- Log aktivitas seluruh akun yang dapat dipantau super admin untuk keperluan monitoring dan audit keseluruhan sistem. 

- Modul pelaporan dan ekspor data ke Excel per BUMDes dan per unit. 

- Aplikasi berbasis web, dioptimalkan untuk kondisi koneksi lambat (retry otomatis, proteksi kehilangan data form). 

Catatan: integrasi payment gateway, mode offline/PWA, dan pengembangan aplikasi mobile untuk sementara dikesampingkan dan tidak menjadi bagian dari fase pengembangan saat ini. Fokus fase ini adalah memastikan modul inti berjalan stabil dengan pencatatan manual dan verifikasi berbasis bukti unggah. 

Halaman 3 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **5. Prinsip Pengelolaan BUMDes (Acuan Nilai Sistem)** 

Sistem yang dikembangkan harus mendukung penerapan enam prinsip pengelolaan BUMDes berikut, sebagaimana diatur dalam Buku Panduan Pengelolaan BUMDesa: 

|**Prinsip**|**Implementasi dalam Sistem**|
|---|---|
|Kooperatif|Fitur referral dan koordinasi antar-BUMDes dalam satu kecamatan untuk<br>mendorong kerja sama.|
|Partisipatif|Akun pengguna/pelanggan dapat memantau data unit yang diikuti; admin unit<br>berkontribusi mencatat data secara rutin.|
|Emansipatif|Hak akses sistem diberikan berdasarkan peran/jabatan, bukan golongan, suku, atau<br>agama.|
|Transparan|Dashboard monitoring dan laporan dapat diakses oleh pengawas, penasihat, dan<br>direktur kapan saja secara real-time, termasuk catatan/feedback yang tercatat<br>terbuka.|
|Akuntabel|Setiap transaksi dan aksi aktivasi/nonaktivasi tercatat dengan log audit (siapa,<br>kapan, aksi apa) dan verifikasi berjenjang untuk bukti pembayaran.|
|Sustainable|Struktur data yang scalable dengan ID unik berjenjang memungkinkan penambahan<br>BUMDes dan unit baru tanpa merombak sistem.|



Halaman 4 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **6. Spesifikasi Pengguna (User Persona & Role)** 

|**Role**|**Jumlah Akun**|**Lingkup Akses**|**Hak Akses Utama**|
|---|---|---|---|
|Super Admin|1|Nasional (seluruh<br>region)|CRUD penuh seluruh modul;<br>aktif/nonaktifkan BUMDes; beri<br>catatan/feedback; pantau log<br>aktivitas seluruh akun|
|Pengawas|3|Nasional (seluruh<br>BUMDes)|Read-only tracking + beri<br>catatan/feedback ke BUMDes/unit|
|Penasihat|1|Nasional (seluruh<br>BUMDes)|Read-only tracking + beri<br>catatan/feedback ke BUMDes/unit|
|Direktur|1|Nasional (seluruh<br>BUMDes)|Read-only tracking + beri<br>catatan/feedback ke BUMDes/unit|
|Admin BUMDes|1 per BUMDes|1 BUMDes|CRUD data BUMDes, kelola referral &<br>iuran, aktif/nonaktifkan unit, kelola<br>akun unit|
|Sekretaris|1 per BUMDes|1 BUMDes|CRUD unit, pelanggan, administrasi<br>surat/notulensi/arsip, input tagihan &<br>pembayaran tunai, kirim<br>pengumuman, ekspor laporan non-<br>keuangan, lihat status iuran (lihat<br>rincian Bab 11.1)|
|Bendahara|1 per BUMDes|1 BUMDes|CRUD keuangan: iuran, kas, tagihan<br>pengguna, dan transaksi unit|
|Admin Unit|1 per unit (5 per<br>BUMDes)|1 unit|CRUD transaksi dan tagihan pada unit<br>yang ditugaskan; aktif/nonaktifkan<br>pelanggan unit|
|Pengguna/Pelanggan|Tidak terbatas|Unit yang diikuti|Read-only: melihat tagihan dan<br>perkembangan data unit/BUMDes<br>yang diikuti; upload bukti transfer|



Catatan: Satu akun admin unit hanya dapat mengelola satu unit (strict 1 akun = 1 unit). Detail rinci hak akses Sekretaris dan Bendahara dijabarkan pada Bab 11.1 dan 11.2. 

Halaman 5 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **7. Alur Bisnis (Business Flow)** 

### **7.1 Alur Pendaftaran & Penambahan BUMDes** 

1. Super admin login ke sistem dan memilih region kecamatan (contoh: Minggir). 

2. Super admin menambahkan data BUMDes baru dengan mengisi kelurahan, nama BUMDes, dan menetapkan status koordinator (khusus Sendangsari sebagai pusat kecamatan). 

3. Super admin membuat akun admin BUMDes untuk BUMDes yang baru ditambahkan. 

4. Admin BUMDes login dan melengkapi data BUMDes, menambahkan unit usaha (dari 5 unit standar atau menambah unit baru), dan membuat akun sekretaris, bendahara, serta admin tiap unit. 

5. Super admin dapat menonaktifkan atau mengaktifkan kembali status BUMDes tertentu apabila dirasa tidak lagi berpengaruh atau tidak aktif beroperasi. 



<!-- Start of picture text -->
Super Admin<br>login ke sistem<br>Pilih region<br>kecamatan (Minggir)<br>Tambah data BUMDes baru<br>(kelurahan, nama, status koordinator)<br>Buat akun<br>Admin BUMDes<br>Admin BUMDes login &<br>lengkapi data BUMDes<br>Tambah unit usaha<br>(5 unit standar / baru)<br>Buat akun Sekretaris,<br>Bendahara, Admin Unit<br><!-- End of picture text -->

_Gambar 7.1 Flowchart Pendaftaran & Penambahan BUMDes_ 

### **7.2 Alur Pencatatan Transaksi per Unit** 

Halaman 6 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

1. Admin unit login ke sistem dan memilih unit yang dikelola. 

2. Admin unit mencatat transaksi input (pemasukan) dan output (pengeluaran) sesuai skema pencatatan unit tersebut. 

3. Sistem menghitung otomatis posisi untung-rugi unit berdasarkan akumulasi transaksi. 

4. Data transaksi tercermin secara real-time pada dashboard BUMDes dan dashboard monitoring nasional. 

5. Admin BUMDes dapat menonaktifkan atau mengaktifkan kembali unit tertentu di BUMDes-nya apabila unit tidak lagi beroperasi. 



<!-- Start of picture text -->
Admin Unit login &<br>pilih unit<br>Input transaksi<br>(Input/Output) sesuai<br>skema unit<br>Sistem hitung otomatis<br>posisi untung-rugi<br>Update real-time:<br>Dashboard BUMDes &<br>Dashboard Nasional<br><!-- End of picture text -->

_Gambar 7.2 Flowchart Pencatatan Transaksi per Unit_ 

Halaman 7 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

### **7.3 Alur Tagihan dan Verifikasi Pembayaran Pengguna** 

1. Admin unit membuat tagihan untuk pengguna terdaftar pada unit tersebut. 

2. Pengguna menerima notifikasi tagihan dan dapat login untuk melihat rincian tagihan. 

3. Pengguna melakukan pembayaran tunai (dicatat manual oleh admin unit) atau transfer (pengguna mengunggah bukti transfer melalui sistem). 

4. Admin unit memverifikasi bukti transfer yang diunggah; status berubah menjadi 'terverifikasi' (masuk) atau 'ditolak' (perlu unggah ulang). 

5. Status tagihan pengguna diperbarui otomatis (lunas/belum lunas/menunggu verifikasi) dan dapat dipantau pengguna melalui akunnya. 

6. Admin unit dapat menonaktifkan atau mengaktifkan kembali status pelanggan/pengguna tertentu pada unit yang dikelola (misal pelanggan berhenti berlangganan). 



<!-- Start of picture text -->
Admin Unit<br>buat tagihan pengguna<br>Pengguna login &<br>lihat rincian tagihan<br>Metode<br>bayar?<br>ransfer<br>Transfer - Pengguna<br>upload bukti transfer<br>Tunai - dicatat<br>manual oleh Admin Unit<br>Admin Unit<br>verifikasi bukti<br>Ya tidak<br>Status: Lunas Status: Ditolak<br>(Terverifikasi) (unggah ulang)<br><!-- End of picture text -->

Halaman 8 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

_Gambar 7.3 Flowchart Tagihan dan Verifikasi Pembayaran_ 

Halaman 9 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

### **7.4 Alur Iuran Bulanan ke Koordinator Kecamatan** 

1. Sistem menghasilkan tagihan iuran otomatis sebesar Rp50.000 untuk setiap BUMDes setiap awal bulan. 

2. Bendahara BUMDes dapat membayar iuran menggunakan saldo kas BUMDes (termasuk dari hasil referral) atau melakukan pembayaran terpisah di luar kas. 

3. Pembayaran iuran diverifikasi oleh admin BUMDes koordinator (Sendangsari). 

4. Status iuran setiap BUMDes (lunas/menunggak) tercermin pada dashboard monitoring koordinator dan pengawas/penasihat/direktur. 



<!-- Start of picture text -->
Sistem generate tagihan<br>iuran RpS0.000/bulan<br>per BUMDes<br>Sumber<br>dana?<br>di luar kas<br>Bayar dari Bayar terpisah<br>saldo Kas BUMDes di luar kas<br>Admin BUMDes Koordinator<br>(Sendangsari) verifikasi<br>Status iuran ter-update<br>pada dashboard monitoring<br><!-- End of picture text -->

_Gambar 7.4 Flowchart Iuran Bulanan ke Koordinator Kecamatan_ 

Halaman 10 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

### **7.5 Alur Referral Antar-BUMDes** 

1. Admin BUMDes pengaju melihat kode referral unik yang tampil pada dashboard BUMDes-nya, dengan masa berlaku 5 hari sejak dibuat. 

2. Kode referral dibagikan kepada BUMDes calon anggota baru (contoh: Sendangsari mengajak Sendangrejo). 

3. Admin BUMDes calon anggota memasukkan kode referral pada menu redeem khusus di sistem. 

4. Sistem memvalidasi kode: memeriksa apakah kode masih berlaku (belum lewat 5 hari) dan belum pernah digunakan sebelumnya. 

5. Setelah redeem berhasil, kode lama otomatis nonaktif dan sistem men-generate kode baru untuk BUMDes pengaju. 

6. Kas referral (Rp10.000) berstatus 'pending' terlebih dahulu, dan baru cair ke kas BUMDes pengaju setelah BUMDes penerima memenuhi syarat aktivitas minimal (contoh: sudah melakukan setor iuran pertama atau tercatat minimal satu transaksi unit) dalam batas waktu 15 hari sejak redeem. 

7. Jika syarat aktivitas tidak terpenuhi hingga 15 hari, status referral berubah menjadi 'gagal' dan kas tidak dicairkan. 

8. Seluruh riwayat pembuatan, redeem, dan status pencairan kode referral tercatat dalam log audit yang dapat dilihat admin BUMDes dan koordinator. 

Halaman 11 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 



<!-- Start of picture text -->
Admin BUMDes pengaju<br>lihat kode referral<br>(berlaku 5 hari) di dashboard<br>Kode dibagikan ke<br>BUMDes calon anggota<br>Admin BUMDes penerima<br>redeem kode<br>Kode valid &<br>belum dipakai?<br>ha dak<br>Redeem berhasil:<br>kode lama nonaktif, (kei en<br>kode baru terbit TEE<br>Kas referral Rp10.000<br>berstatus PENDING<br>‘Aktivitas minimal<br>terpenuhi dim 15 hari?<br>fra tidak<br>Kas CAIR ke Status referral GAGAL,<br>BUMDes pengaju kas tidak dicairkan<br><!-- End of picture text -->

_Gambar 7.5 Flowchart Referral Antar-BUMDes (masa berlaku kode 5 hari, batas verifikasi aktivitas 15 hari)_ 

Halaman 12 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

### **7.6 Alur Monitoring Operasional & Feedback** 

1. Pengawas, penasihat, direktur, dan super admin login dengan akses read-only tingkat nasional (ditambah kemampuan memberi catatan/feedback). 

2. Dashboard menampilkan status kesehatan setiap BUMDes: status iuran bulan berjalan, saldo kas, ringkasan untung-rugi tiap unit, dan aktivitas transaksi terakhir. 

3. Sistem menampilkan indikator/peringatan otomatis apabila: BUMDes menunggak iuran, unit tidak ada transaksi dalam periode tertentu, atau terdapat bukti transfer yang belum diverifikasi dalam jangka waktu lama. 

4. Pengawas/penasihat/direktur/super admin dapat memberikan catatan atau feedback langsung ke BUMDes tertentu atau ke unit tertentu di dalam BUMDes tersebut. 

5. Sistem mengirimkan notifikasi secara real-time kepada pengurus BUMDes/unit terkait begitu catatan/feedback baru diterima. 

6. Pengurus BUMDes/unit menindaklanjuti catatan tersebut, dan status tindak lanjut (belum/sedang/selesai ditindaklanjuti) dapat dipantau kembali oleh pemberi catatan. 



<!-- Start of picture text -->
Pengawas / Penasihat /<br>Direktur / Super Admin login<br>(akses read-only + feedback)<br>Lihat dashboard nasional:<br>status tiap BUMDes & unit<br>Sistem tampilkan<br>indikator/peringatan otomatis<br>Beri catatan/feedback<br>ke BUMDes atau unit tertentu<br>Notifikasi real-time<br>diterima pengurus BUMDes/unit<br>Pengurus tindak lanjuti<br>& perbarui status catatan<br><!-- End of picture text -->

_Gambar 7.6 Flowchart Monitoring Operasional dan Pemberian Catatan/Feedback_ 

Halaman 13 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **8. Alur Sistem (System Flow)** 

### **8.1 Alur Data Antar Modul** 

Secara garis besar, alur data dalam sistem mengikuti pola berikut: 

1. Modul Region (provinsi/kota/kecamatan/kelurahan) menjadi induk data bagi Modul BUMDes. 

2. Modul BUMDes menjadi induk data bagi Modul Unit Usaha (5 unit atau lebih). 

3. Modul Unit Usaha menghasilkan data transaksi yang menjadi sumber bagi Modul Tagihan Pengguna dan Modul Laporan Keuangan Unit. 

4. Modul Iuran & Referral terhubung ke Modul BUMDes (bukan ke unit), dengan Modul Kas BUMDes sebagai penampung dana dari iuran dan referral. 

5. Modul Laporan mengagregasi data dari Modul Unit Usaha dan Modul Iuran & Referral untuk menghasilkan laporan per BUMDes dan per unit (termasuk ekspor Excel). 

6. Modul Dashboard Monitoring menarik data teragregasi dari seluruh modul di atas secara real-time untuk ditampilkan sesuai lingkup akses tiap role. 

7. Modul Feedback/Catatan terhubung ke Dashboard Monitoring, memungkinkan pengawas/penasihat/direktur/super admin memberi catatan yang diteruskan sebagai notifikasi ke Modul BUMDes/Unit. 

8. Modul Log Audit Aktivitas mencatat setiap aksi CRUD, proses verifikasi, dan aktivitas login/logout dari seluruh modul di atas untuk keperluan transparansi, akuntabilitas, dan monitoring akun oleh super admin. 



<!-- Start of picture text -->
eas Taran Mode<br>= eres sate<br>\\ mee Modul Laperan<br>\\ te Spore<br>\y Sooo e222. (ina recaoen<br>N ‘Modul Dashboard<br>™ —<br>of Modul furan |/_—————<br>reel<br><!-- End of picture text -->

_Gambar 8.1 Diagram Alur Data Antar Modul_ 

Halaman 14 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

### **8.2 Diagram Alur Tingkat Tinggi (End-to-End)** 

1. Super admin mengelola region dan BUMDes, lalu admin BUMDes mengelola unit, sekretaris, dan bendahara. 

2. Admin unit mencatat transaksi dan tagihan, yang direspons pengguna melalui pembayaran atau unggah bukti transfer. 

3. Data transaksi unit diagregasi menjadi laporan unit, lalu laporan BUMDes, hingga tampil pada dashboard monitoring nasional. 

4. Secara paralel, admin BUMDes mengelola referral dan iuran yang bermuara pada kas BUMDes, yang sebagian digunakan untuk membayar iuran ke koordinator kecamatan (Sendangsari). 



<!-- Start of picture text -->
a a es<br>"| duran Reterray)_ |}-——— \etoatanran ke Koordinator eegr)<br><!-- End of picture text -->

_Gambar 8.2 Diagram Alur Tingkat Tinggi End-to-End_ 

Halaman 15 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **9. Kebutuhan Fungsional** 

### **9.1 Modul Manajemen Region** 

|**ID**|**Kebutuhan**|**Prioritas**|
|---|---|---|
|FR-01|Sistem dapat menyimpan struktur wilayah berjenjang: provinsi, kota,<br>kecamatan, kelurahan, masing-masing dengan ID unik.|Tinggi|
|FR-02|Super admin dapat menambah, mengubah, menghapus data region.|Tinggi|
|FR-03|Sistem dapat menandai satu kelurahan sebagai koordinator pusat dalam satu<br>kecamatan.|Tinggi|



### **9.2 Modul Manajemen BUMDes & Unit** 

|**ID**|**Kebutuhan**|**Prioritas**|
|---|---|---|
|FR-04|Super admin/admin dapat menambah BUMDes baru pada kelurahan tertentu,<br>dengan ID unik per BUMDes.|Tinggi|
|FR-05|Admin BUMDes dapat menambah, mengubah, menonaktifkan unit usaha<br>(minimal 5 unit standar, dapat ditambah), masing-masing dengan ID unik.|Tinggi|
|FR-06|Setiap unit memiliki skema field pencatatan transaksi yang dapat dikonfigurasi<br>berbeda-beda (fleksibel per jenis unit).|Tinggi|
|FR-07|Sistem menghitung posisi untung-rugi tiap unit secara otomatis berdasarkan<br>transaksi input-output.|Tinggi|
|FR-08|Super admin dapat menonaktifkan/mengaktifkan kembali BUMDes tertentu.|Tinggi|
|FR-09|Admin BUMDes dapat menonaktifkan/mengaktifkan kembali unit tertentu di<br>BUMDes-nya.|Tinggi|
|FR-10|Admin unit dapat menonaktifkan/mengaktifkan kembali status<br>pelanggan/pengguna pada unit yang dikelola.|Tinggi|



### **9.3 Modul Tagihan & Verifikasi Pembayaran** 

|**ID**|**Kebutuhan**|**Prioritas**|
|---|---|---|
|FR-11|Admin unit dapat membuat tagihan untuk pengguna terdaftar pada unit<br>tersebut.|Tinggi|
|FR-12|Pengguna dapat melihat status tagihan miliknya melalui akun masing-masing.|Tinggi|
|FR-13|Pengguna dapat mengunggah bukti transfer pembayaran.|Tinggi|
|FR-14|Admin unit dapat memverifikasi atau menolak bukti transfer yang diunggah<br>pengguna.|Tinggi|
|FR-15|Admin unit/bendahara dapat mencatat pembayaran tunai secara manual.|Sedang|



### **9.4 Modul Iuran & Referral** 

|**ID**|**Kebutuhan**|**Prioritas**|
|---|---|---|
|FR-16|Sistem membuat tagihan iuran otomatis Rp50.000 per BUMDes setiap bulan.|Tinggi|



Halaman 16 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

|**ID**|**Kebutuhan**|**Prioritas**|
|---|---|---|
|FR-17|Bendahara dapat membayar iuran menggunakan saldo kas BUMDes atau<br>mencatat pembayaran di luar kas.|Tinggi|
|FR-18|Sistem men-generate kode referral unik per BUMDes dengan masa berlaku 5<br>hari, tampil di dashboard.|Tinggi|
|FR-19|Kode referral otomatis nonaktif dan digantikan kode baru setelah digunakan<br>atau kedaluwarsa.|Tinggi|
|FR-20|Sistem menyediakan menu/form redeem kode referral bagi BUMDes<br>penerima.|Tinggi|
|FR-21|Kas referral berstatus pending hingga syarat aktivitas minimal BUMDes<br>penerima terpenuhi dalam 15 hari, baru dicairkan.|Tinggi|
|FR-22|Sistem mencatat log lengkap: pembuat kode, penerima, waktu redeem, dan<br>status pencairan.|Tinggi|



### **9.5 Modul Akun & Hak Akses** 

|**ID**|**Kebutuhan**|**Prioritas**|
|---|---|---|
|FR-23|Sistem menerapkan kontrol akses berbasis peran (role-based access control)<br>sesuai matriks role pada Bab 11.|Tinggi|
|FR-24|Super admin dapat membuat, mengubah, menonaktifkan seluruh akun<br>pengguna sistem.|Tinggi|
|FR-25|Admin BUMDes dapat membuat akun sekretaris, bendahara, dan admin unit<br>di BUMDes-nya.|Tinggi|
|FR-26|Satu akun admin unit hanya terhubung ke satu unit usaha.|Tinggi|
|FR-27|Setiap akun (pengguna, pelanggan, admin, dsb.) memiliki ID unik yang<br>konsisten di seluruh sistem.|Tinggi|



### **9.6 Modul Laporan & Dashboard Monitoring** 

|**ID**|**Kebutuhan**|**Prioritas**|
|---|---|---|
|FR-28|Sistem dapat menghasilkan laporan per unit dan per BUMDes yang dapat<br>diekspor ke format Excel dengan format rapi.|Tinggi|
|FR-29|Dashboard monitoring menampilkan status kesehatan operasional tiap<br>BUMDes secara real-time (khusus role nasional).|Tinggi|
|FR-30|Sistem menampilkan indikator/peringatan otomatis untuk BUMDes<br>menunggak iuran, unit tanpa aktivitas, atau verifikasi tertunda.|Sedang|
|FR-31|Sistem menyediakan log audit aktivitas seluruh pengguna sistem.|Tinggi|
|FR-32|Super admin dapat melihat log status aktivitas setiap akun (login, aksi, waktu)<br>untuk keperluan monitoring keseluruhan sistem.|Tinggi|



### **9.7 Modul Feedback / Catatan** 

Halaman 17 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

|**ID**|**Kebutuhan**|**Prioritas**|
|---|---|---|
|FR-33|Pengawas, penasihat, direktur, dan super admin dapat memberikan<br>catatan/feedback ke BUMDes tertentu atau unit tertentu di dalamnya.|Tinggi|
|FR-34|Sistem mengirim notifikasi real-time kepada pengurus BUMDes/unit terkait<br>saat catatan/feedback baru diterima.|Tinggi|
|FR-35|Pengurus BUMDes/unit dapat memperbarui status tindak lanjut atas catatan<br>yang diterima (belum/sedang/selesai ditindaklanjuti).|Sedang|
|FR-36|Pemberi catatan dapat memantau status tindak lanjut atas catatan yang<br>pernah diberikan.|Sedang|



Halaman 18 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **10. Kebutuhan Non-Fungsional** 

|**Aspek**|**Kebutuhan**|
|---|---|
|Keamanan|Autentikasi berbasis akun dan role; enkripsi password; proteksi terhadap akses<br>lintas-BUMDes yang tidak sah; validasi upload file bukti transfer.|
|Skalabilitas|Struktur data dengan ID unik berjenjang mendukung penambahan region,<br>BUMDes, dan unit baru tanpa perubahan struktur besar; dirancang untuk<br>berkembang dari 2 kelurahan ke seluruh kecamatan/kabupaten.|
|Kemudahan Pengguna|Antarmuka sederhana dan intuitif, dapat digunakan lintas usia dan tingkat<br>literasi digital, khususnya untuk admin unit dan pengguna di desa.|
|Transparansi & Akuntabilitas|Log audit pada setiap transaksi dan aksi penting (termasuk aktivasi/nonaktivasi<br>dan feedback); laporan dapat diakses sesuai kewenangan role.|
|Integrasi Sistem|Arsitektur modular agar mudah diintegrasikan dengan sistem lain pada fase<br>berikutnya bila diperlukan.|
|Performa|Sistem responsif untuk operasi CRUD dan laporan meski pada kondisi koneksi<br>internet terbatas (khas pedesaan).|
|Ketahanan Koneksi Lambat|Mekanisme retry otomatis pada saat submit gagal dan proteksi agar data form<br>tidak hilang saat koneksi terputus (web online-only dengan UX toleran koneksi<br>lambat).|
|Keberlanjutan & Dukungan|Dokumentasi teknis lengkap, kode terstruktur, dan containerization (Docker)<br>agar mudah dipelihara dan direplikasi oleh tim pengembang berikutnya.|



Halaman 19 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **11. Matriks Akses (Role & Module Access Matrix)** 

|**Modul**|**Super**<br>**Admin**|**Pengawas/Penasihat/Dire**|**ktur**<br>**Admin BUMDes**|**Sekretaris**|**Bendahara**|**Adm**|
|---|---|---|---|---|---|---|
|Region|CRUD|Read|-|-|-|-|
|BUMDes (termasuk<br>aktif/nonaktif)|CRUD|Read|Read/Update<br>(milik sendiri)|Read|Read|-|
|Unit Usaha (termasuk<br>aktif/nonaktif)|CRUD|Read|CRUD (di BUMDes-<br>nya)|CRUD|Read|Read<br>send|
|Pelanggan Unit<br>(termasuk<br>aktif/nonaktif)|CRUD|Read|Read|CRUD|Read|CRUD<br>send|
|Transaksi Unit|CRUD|Read|Read|Read|CRUD|CRUD<br>send|
|Tagihan Pengguna|CRUD|Read|Read|CRUD (input<br>tagihan &<br>tunai)|CRUD|CRUD<br>send|
|Iuran & Kas BUMDes|CRUD|Read|CRUD|Read (status<br>iuran)|CRUD|-|
|Referral|CRUD|Read|CRUD<br>(generate/redeem)|Read|Read|-|
|Akun Pengguna Sistem|CRUD|-|CRUD (di BUMDes-<br>nya)|-|-|-|
|Administrasi (surat,<br>notulensi, arsip)|CRUD|Read|Read|CRUD|Read|-|
|Pengumuman/Notifikasi<br>ke Pelanggan|CRUD|-|CRUD|CRUD<br>(kirim)|Read|Read|
|Feedback/Catatan|CRUD|CRUD (beri catatan)|Read + tindak<br>lanjut|Read +<br>tindak<br>lanjut|Read +<br>tindak<br>lanjut|Read<br>tinda<br>lanju|
|Log Aktivitas Akun|Read<br>(seluruh<br>akun)|-|-|-|-|-|
|Laporan & Ekspor Excel|Read/Export|Read/Export|Read/Export|Read/Export<br>(data<br>pelanggan,<br>tagihan,<br>kegiatan<br>unit, arsip)|Read/Export<br>(keuangan)|Read<br>(unit<br>send|
|Dashboard Monitoring|Penuh|Penuh (nasional)|BUMDes sendiri|BUMDes<br>sendiri|BUMDes<br>sendiri|Unit|



### **11.1 Rincian Hak Akses Sekretaris** 

Peran sekretaris diperluas untuk mendukung fungsi administratif dan operasional non-keuangan BUMDes, dengan rincian akses sebagai berikut: 

Halaman 20 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

- CRUD data unit usaha di BUMDes-nya (menambah/mengubah data unit, bukan data keuangan unit). 

- CRUD data pelanggan/pengguna terdaftar di setiap unit. 

- Administrasi: input dan kelola surat masuk/keluar digital, notulensi rapat, serta arsip dokumen. 

- Tagihan: input tagihan pelanggan dan input pembayaran tunai yang diterima langsung, serta cetak bukti pembayaran. 

- Pengumuman/notifikasi: mengirim pengumuman atau pengingat tagihan kepada pelanggan. 

- Laporan: ekspor laporan ke Excel untuk data pelanggan, tagihan, kegiatan unit, dan arsip. 

- Iuran/referral: melihat status iuran bulanan BUMDes (read-only, tidak dapat mengubah). 

### **11.2 Rincian Hak Akses Bendahara** 

Peran bendahara difokuskan pada seluruh aspek keuangan BUMDes, dengan rincian akses sebagai berikut: 

- CRUD data iuran bulanan BUMDes dan kas BUMDes (termasuk pencairan referral). 

- CRUD tagihan pengguna (dapat membuat, mengubah, memverifikasi tagihan dan bukti pembayaran di seluruh unit BUMDes-nya). 

- CRUD transaksi unit (input-output dan hasil untung-rugi tiap unit di BUMDes-nya). 

- Ekspor laporan keuangan ke Excel. 

Halaman 21 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **12. Rekomendasi Teknis** 

Rekomendasi berikut disusun berdasarkan keahlian tim (PHP/Laravel), kebutuhan fase percobaan berbasis web, serta kebutuhan performa pada kondisi koneksi terbatas di pedesaan. 

### **12.1 Backend Framework** 

|**Komponen**|**Rekomendasi**|**Alasan**|
|---|---|---|
|Framework Backend|Laravel 11.x (PHP 8.2+)|Sesuai keahlian tim; ekosistem matang;<br>mendukung deployment cepat; memiliki fitur<br>bawaan untuk auth, queue, dan validasi.|
|Autentikasi & Otorisasi|Laravel Breeze/Sanctum + Spatie<br>Laravel-Permission|Sanctum cocok untuk autentikasi berbasis sesi<br>pada aplikasi web; Spatie Permission<br>memudahkan implementasi role-based access<br>control multi-level (super admin hingga<br>pengguna).|
|Antrian & Job|Laravel Queue (database driver di<br>fase awal)|Untuk proses non-real-time seperti generate<br>laporan Excel dan notifikasi tagihan/feedback,<br>tanpa perlu Redis di fase percobaan.|



### **12.2 Frontend** 

|**Komponen**|**Rekomendasi**|**Alasan**|
|---|---|---|
|Templating/UI|Laravel Blade + Livewire|Memungkinkan interaktivitas (form<br>dinamis, validasi real-time, notifikasi real-<br>time) tanpa membangun API terpisah,<br>mempercepat pengembangan untuk tim<br>kecil dengan keahlian PHP.|
|CSS Framework|Tailwind CSS|Ringan, mudah dikustomisasi, umum<br>dipasangkan dengan Blade/Livewire,<br>mendukung desain responsif untuk<br>pengguna di berbagai perangkat.|
|Optimisasi Koneksi<br>Lambat|Livewire loading states + retry logic<br>pada request, kompresi asset (Vite<br>build)|Mendukung keputusan web online-only:<br>mencegah kehilangan data form dan<br>memberi umpan balik yang jelas saat<br>koneksi lambat.|



### **12.3 Penggunaan FilamentPHP untuk Admin Panel** 

FilamentPHP dapat dipertimbangkan dan sangat memungkinkan untuk diterapkan pada proyek ini, karena dibangun di atas Livewire sehingga selaras dengan pilihan stack Blade + Livewire yang sudah ditetapkan. Rekomendasi penerapannya sebagai berikut: 

- Gunakan Filament untuk panel internal yang bersifat manajerial dan data-heavy: panel Super Admin (kelola region, BUMDes, unit, akun), panel Admin BUMDes/Sekretaris/Bendahara (CRUD unit, pelanggan, transaksi, iuran, referral), serta dashboard read-only untuk Pengawas/Penasihat/Direktur. 

- Filament menyediakan komponen siap pakai (tabel data, form builder, widget statistik, notifikasi) yang mempercepat pengembangan dashboard monitoring dan CRUD tanpa membangun UI dari nol. 

Halaman 22 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

- Gunakan Blade + Livewire custom (di luar Filament) untuk portal Pengguna/Pelanggan yang bersifat lebih sederhana dan publik-facing, seperti cek tagihan dan upload bukti transfer, agar tampilan lebih ringan dan disesuaikan kebutuhan pengguna awam. 

• Filament mendukung multi-panel dalam satu instalasi Laravel, sehingga panel Super Admin, Admin BUMDes, dan panel Monitoring dapat dipisahkan secara logis namun tetap dalam satu basis kode. Dengan pendekatan ini, sebagian besar kebutuhan CRUD dan dashboard pada Bab 9 dapat dipercepat pengembangannya menggunakan Filament, sementara bagian yang membutuhkan kustomisasi tinggi (misalnya alur redeem referral atau upload bukti transfer) tetap dibangun dengan Livewire custom. 

### **12.4 Database** 

|**Komponen**|**Rekomendasi**|**Alasan**|
|---|---|---|
|Database Utama|MySQL 8.x|Kompatibel penuh dengan Laravel,<br>banyak didukung hosting/VPS lokal,<br>cukup untuk skala data fase percobaan<br>(2 kelurahan).|
|Struktur Data Fleksibel per<br>Unit|Kombinasi tabel relasional inti +<br>kolom JSON (schema-less) untuk field<br>khusus tiap unit|Mengakomodasi kebutuhan skema<br>pencatatan berbeda per unit (FR-06)<br>tanpa membuat tabel terpisah untuk<br>tiap jenis unit, sambil tetap menjaga<br>integritas relasi data inti.|
|Migrasi & Seeding|Laravel Migration & Seeder|Memudahkan replikasi struktur<br>database saat sistem diperluas ke<br>kelurahan/kecamatan lain pada fase<br>berikutnya.|



### **12.5 Library & Tools Pendukung** 

|**Kebutuhan**|**Library/Tools**|
|---|---|
|Admin Panel|filament/filament|
|Ekspor Excel|maatwebsite/excel (Laravel Excel)|
|Upload & Validasi File Bukti Transfer|Laravel Storage (local/public disk di fase awal) +<br>validasi mime-type dan ukuran file bawaan Laravel|
|Log Audit|spatie/laravel-activitylog|
|Notifikasi Real-time (tagihan, verifikasi, feedback)|Laravel Notification (database & broadcast channel) +<br>Laravel Echo/Pusher atau Laravel Reverb|
|Grafik Dashboard Monitoring|Chart.js atau ApexCharts (terintegrasi widget<br>Filament)|
|Testing|PHPUnit / Pest untuk unit dan feature testing|
|Version Control & CI dasar|Git + GitHub Actions (opsional untuk fase percobaan)|



Halaman 23 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

### **12.6 Containerization dengan Docker** 

Untuk memudahkan deployment, replikasi lingkungan pengembangan, dan keberlanjutan proyek, sistem direkomendasikan dikemas menggunakan Docker dengan susunan layanan sebagai berikut: 

|**Service**|**Image/Base**|**Fungsi**|
|---|---|---|
|app|php:8.2-fpm|Menjalankan aplikasi Laravel (PHP-FPM)|
|webserver|nginx:stable-alpine|Reverse proxy dan penyaji request HTTP ke app|
|db|mysql:8.0|Database utama MySQL|
|redis (opsional, fase 2)|redis:7-alpine|Cache dan session store bila skala meningkat|
|node (build-time)|node:20-alpine|Build asset frontend (Vite, Tailwind) sebelum<br>deployment|
|queue-worker|php:8.2-fpm (image sama<br>dengan app)|Menjalankan Laravel Queue Worker untuk job<br>Excel/notifikasi|



**Contoh Struktur docker-compose.yml (Ringkasan)** 

- services: app, webserver, db, queue-worker (redis ditambahkan pada fase berikutnya bila diperlukan). 

- app dan queue-worker berbagi volume kode aplikasi (./ -> /var/www) dan file .env yang sama. 

- webserver memetakan port 80 (dan 443 bila menggunakan SSL) ke host, dengan konfigurasi Nginx mengarah ke php-fpm pada app. 

- db menggunakan volume persist (db-data) agar data tidak hilang saat container di-restart, dengan environment MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD. 

- Jaringan internal (bridge network) menghubungkan seluruh service agar dapat saling berkomunikasi menggunakan nama service (misal host database diisi 'db'). 

**Spesifikasi Server yang Direkomendasikan (Fase Percobaan)** 

|**Komponen**|**Spesifikasi Minimum**|
|---|---|
|CPU|2 vCPU|
|RAM|4 GB|
|Storage|40-60 GB SSD (termasuk ruang untuk file bukti transfer dan backup database)|
|Sistem Operasi Host|Ubuntu Server 22.04 LTS (kompatibel Docker Engine & Docker Compose)|
|Bandwidth|Cukup untuk trafik skala 2 kelurahan; direkomendasikan minimal 10 Mbps<br>simetris|



Spesifikasi ini disesuaikan untuk fase percobaan (2 kelurahan). Saat sistem diperluas ke seluruh kecamatan atau kabupaten, spesifikasi CPU/RAM dan strategi container (misalnya penambahan load balancer atau orkestrasi dengan Docker Swarm/Kubernetes) perlu ditinjau ulang. 

### **12.7 Hosting & Infrastruktur** 

Karena opsi hosting masih terbuka, untuk fase percobaan direkomendasikan VPS dengan spesifikasi pada Bab 12.6 yang menjalankan seluruh service melalui Docker Compose. Pilihan penyedia dapat berupa layanan cloud terjangkau (misal DigitalOcean, Biznet Gio, atau VPS lokal Indonesia) untuk latensi lebih baik ke pengguna di desa. Jika kelak sistem diperluas ke seluruh kecamatan/kabupaten, migrasi ke layanan managed cloud (AWS/GCP) dengan load balancer dapat dipertimbangkan. 

Halaman 24 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

Halaman 25 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **13. Struktur ID Unik** 

Untuk menjaga konsistensi dan kemudahan penelusuran data di seluruh sistem, setiap entitas utama diberi ID unik dengan pola prefix + kode berjenjang, sebagai berikut: 

|**Entitas**|**Contoh Format ID**|**Keterangan**|
|---|---|---|
|Region<br>(Provinsi/Kota/Kecamatan/Kelurahan)|REG-3404-070-005|Mengikuti pola kode wilayah administratif<br>berjenjang, dapat merujuk kode wilayah<br>resmi Kemendagri.|
|BUMDes|BMD-SDS-001|Prefix BMD + kode singkat kelurahan +<br>nomor urut.|
|Unit Usaha|UNT-SDS-PAM-01|Prefix UNT + kode BUMDes + kode jenis<br>unit + nomor urut.|
|Akun (seluruh role)|AKN-000123|Prefix AKN + nomor urut global, terlepas<br>dari role.|
|Pelanggan/Pengguna Unit|PLG-SDS-PAM-0045|Prefix PLG + kode BUMDes + kode unit +<br>nomor urut.|
|Transaksi Unit|TRX-20260916-000345|Prefix TRX + tanggal transaksi + nomor<br>urut harian.|
|Tagihan|TAG-20260916-000112|Prefix TAG + tanggal terbit + nomor urut<br>harian.|
|Kode Referral|REF-SDS-A1B2C3|Prefix REF + kode BUMDes pengaju + kode<br>acak unik (mencegah duplikasi/tebakan).|
|Catatan/Feedback|FB-000078|Prefix FB + nomor urut global.|



Skema penomoran ini menjadi acuan awal dan dapat disesuaikan pada tahap desain teknis lanjutan, namun prinsip 'setiap entitas memiliki ID unik dan mudah ditelusuri sumbernya' wajib dipertahankan. 

Halaman 26 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **14. Desain Database (Entity Relationship Diagram)** 

Diagram berikut menggambarkan rancangan awal struktur basis data beserta relasi antar entitas utama dalam sistem, sebagai acuan pada tahap desain teknis lanjutan. 



<!-- Start of picture text -->
| 3— : |<br><!-- End of picture text -->

_Gambar 14.1 Entity Relationship Diagram (ERD) SIM-BUMDes_ 

### **14.1 Penjelasan Entitas Utama** 

|**Entitas**|**Deskripsi Singkat**|
|---|---|
|REGION|Struktur wilayah berjenjang (self-referencing) dari provinsi hingga kelurahan, termasuk<br>penanda koordinator kecamatan.|
|BUMDES|Data BUMDes per kelurahan, termasuk status aktif/nonaktif.|
|AKUN|Seluruh akun pengguna sistem lintas role, terhubung opsional ke BUMDes dan/atau<br>unit sesuai peran.|
|UNIT_USAHA|Unit usaha di bawah satu BUMDes, dengan skema field transaksi fleksibel (JSON) dan<br>status aktif/nonaktif.|
|TRANSAKSI|Catatan input/output tiap unit usaha, sumber perhitungan untung-rugi.|
|PELANGGAN|Data pengguna/pelanggan yang mengikuti satu unit tertentu, dengan status<br>aktif/nonaktif dan akun opsional untuk self-service.|
|TAGIHAN|Tagihan yang diterbitkan untuk pelanggan pada unit tertentu, termasuk status<br>verifikasi dan bukti transfer.|
|IURAN_BUMDES|Catatan iuran bulanan wajib Rp50.000 tiap BUMDes ke koordinator kecamatan.|
|KAS_BUMDES|Saldo dan riwayat mutasi kas tiap BUMDes (gabungan dari iuran dan referral).|
|REFERRAL|Riwayat kode referral antar-BUMDes, mencatat pengaju, penerima, masa berlaku (5<br>hari), dan batas verifikasi aktivitas (15 hari).|



Halaman 27 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

|**Entitas**|**Deskripsi Singkat**|
|---|---|
|FEEDBACK|Catatan/feedback dari pengawas/penasihat/direktur/super admin ke BUMDes atau<br>unit tertentu, beserta status tindak lanjut.|
|LOG_AKTIVITAS|Log seluruh aktivitas akun (login, CRUD, verifikasi) untuk keperluan audit dan<br>monitoring oleh super admin.|



Desain ini bersifat rancangan awal (high-level) dan akan disempurnakan lebih lanjut (termasuk penentuan 

tipe data, indeks, dan constraint detail) pada tahap perancangan teknis database. 

Halaman 28 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **15. Roadmap Fase Pengembangan** 

|**Fase**|**Cakupan**|**Target Output**|
|---|---|---|
|Fase 1 - Percobaan (saat ini)|2 kelurahan (Sendangsari & Sendangrejo), 5 unit<br>standar, seluruh modul in-scope pada Bab 4.1|Sistem berjalan dan diuji<br>coba operasional harian<br>oleh admin BUMDes dan<br>admin unit|
|Fase 2 - Penyempurnaan|Evaluasi hasil uji coba, perbaikan bug,<br>penyesuaian skema unit berdasarkan masukan<br>lapangan|Sistem stabil dan siap<br>direplikasi|
|Fase 3 - Perluasan Wilayah|Ekspansi ke 3 kelurahan tersisa di Kecamatan<br>Minggir (Sendangagung, Sendangarum,<br>Sendangmulyo)|Seluruh kecamatan<br>terhubung dalam satu<br>sistem|



Catatan: peningkatan fitur lanjutan seperti integrasi payment gateway, mode offline/PWA, dan aplikasi 

mobile untuk sementara dikesampingkan dan belum dijadwalkan pada roadmap ini; akan dipertimbangkan kembali setelah Fase 3 berjalan stabil. 

Halaman 29 dari 30 

BRD - Sistem Informasi Manajemen BUMDes 

## **16. Lampiran** 

### **16.1 Detail Mekanisme Referral** 

- Masa berlaku kode referral: 5 hari (1 minggu kerja) sejak digenerate. 

- Kode referral otomatis berganti setelah digunakan (redeem) satu kali atau setelah kedaluwarsa. 

- Nominal kas referral: Rp10.000 per referral berhasil, berstatus pending sampai syarat aktivitas minimal BUMDes penerima terpenuhi. 

- Batas waktu verifikasi aktivitas: 15 hari sejak redeem (telah disepakati). 

- Seluruh proses referral tercatat dalam log audit yang dapat ditelusuri oleh admin BUMDes dan koordinator kecamatan. 

### **16.2 Contoh Struktur Wilayah Fase 1** 

|**Provinsi**|**Kota/Kabupaten**|**Kecamatan**|**Kelurahan**|**Status**|
|---|---|---|---|---|
|DI Yogyakarta|Kabupaten Sleman|Minggir|Sendangsari|Koordinator Pusat<br>Kecamatan|
|DI Yogyakarta|Kabupaten Sleman|Minggir|Sendangrejo|BUMDes Anggota (Uji<br>Coba Referral)|
|DI Yogyakarta|Kabupaten Sleman|Minggir|Sendangagung|Belum Aktif (Fase<br>Berikutnya)|
|DI Yogyakarta|Kabupaten Sleman|Minggir|Sendangarum|Belum Aktif (Fase<br>Berikutnya)|
|DI Yogyakarta|Kabupaten Sleman|Minggir|Sendangmulyo|Belum Aktif (Fase<br>Berikutnya)|



### **16.3 Catatan untuk Diskusi Lanjutan** 

- Perlu disepakati detail field pencatatan transaksi untuk masing-masing dari 5 unit usaha (PAMDes, peternakan, mitra tani, penyewaan mobil, sampah) pada tahap desain database lanjutan. 

- Perlu disepakati format dan periode laporan (mingguan/bulanan) yang akan diekspor ke Excel. 

- Perlu ditentukan pola kode wilayah resmi (REG-xxxx) yang akan dipakai, apakah mengikuti kode Kemendagri atau kode internal sistem. 

- Dokumen ini bersifat draft dan terbuka untuk direvisi berdasarkan hasil diskusi lebih lanjut dengan pemangku kepentingan. 

Halaman 30 dari 30 

