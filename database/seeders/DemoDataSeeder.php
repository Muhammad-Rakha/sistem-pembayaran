<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TagihanSpp;
use App\Models\TahunAjaran;
use App\Models\TarifSpp;
use App\Models\User;
use App\Models\VirtualAccount;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * DemoDataSeeder
 *
 * NIS format: YYJNN
 *   YY = two-digit enrollment year (24 = 2024, 25 = 2025, 26 = 2026)
 *   J  = major code (1 = RPL, 2 = TKJ, 3 = AKL)
 *   NN = two-digit sequence per major and enrollment year (01, 02, ...)
 *
 * Examples:
 *   24101 = first RPL student enrolled in 2024
 *   25201 = first TKJ student enrolled in 2025
 *   26301 = first AKL student enrolled in 2026
 *
 * The cohorts are mapped to the current academic year (2026/2027):
 *   2024 cohort -> grade XII
 *   2025 cohort -> grade XI
 *   2026 cohort -> grade X
 *
 * Virtual Account format (BR-02): '88000' + the last five digits of the NIS.
 *   24101 -> 8800024101
 */
class DemoDataSeeder extends Seeder
{
    private const VA_PREFIX      = '88000';
    private const TAHUN_AJARAN   = '2026/2027';
    private const TARIF_NOMINAL  = 350_000;

    /** Major definitions: code => [short name, full name] */
    private const MAJORS = [
        1 => ['RPL', 'Rekayasa Perangkat Lunak'],
        2 => ['TKJ', 'Teknik Komputer dan Jaringan'],
        3 => ['AKL', 'Akuntansi dan Keuangan Lembaga'],
    ];

    /** Enrollment year => grade in the current academic year. */
    private const COHORTS = [
        2024 => 'XII',
        2025 => 'XI',
        2026 => 'X',
    ];

    /**
     * Students grouped by major code and enrollment year.
     *
     * Each entry is [nama, jenis_kelamin]. The array order determines the
     * two-digit NIS sequence, so the first RPL 2026 student gets 26101.
     */
    private const STUDENTS = [
        // RPL (major code 1)
        1 => [
            2024 => [
                ['Ahmad Fauzan', 'L'],
                ['Siti Aminah', 'P'],
                ['Budi Santoso', 'L'],
                ['Citra Lestari', 'P'],
                ['Dimas Pratama', 'L'],
                ['Eka Putri', 'P'],
                ['Fajar Rizki', 'L'],
                ['Gita Nuraini', 'P'],
                ['Hendra Wijaya', 'L'],
                ['Indah Permata', 'P'],
            ],
            2025 => [
                ['Joko Susilo', 'L'],
                ['Kartika Dewi', 'P'],
                ['Lukman Hakim', 'L'],
                ['Maya Sari', 'P'],
                ['Nanda Setiawan', 'L'],
                ['Oktavia Rahma', 'P'],
                ['Putra Aditya', 'L'],
                ['Qori Ramadhani', 'P'],
                ['Reza Firmansyah', 'L'],
                ['Sania Putri', 'P'],
            ],
            2026 => [
                ['Taufik Hidayat', 'L'],
                ['Umi Kalsum', 'P'],
                ['Vino Bastian', 'L'],
                ['Wulan Sari', 'P'],
                ['Yoga Pratama', 'L'],
                ['Zahra Nabila', 'P'],
                ['Arif Rahman', 'L'],
                ['Bunga Citra', 'P'],
                ['Candra Kurniawan', 'L'],
                ['Dewi Anggraini', 'P'],
            ],
        ],

        // TKJ (major code 2)
        2 => [
            2024 => [
                ['Eko Prasetyo', 'L'],
                ['Fitri Handayani', 'P'],
                ['Galih Permana', 'L'],
                ['Hana Safitri', 'P'],
                ['Irfan Maulana', 'L'],
                ['Juliana Putri', 'P'],
                ['Kevin Ardiansyah', 'L'],
                ['Lilis Suryani', 'P'],
                ['Mochammad Rizal', 'L'],
                ['Nur Aisyah', 'P'],
            ],
            2025 => [
                ['Oscar Mahendra', 'L'],
                ['Puspita Dewi', 'P'],
                ['Qomaruddin', 'L'],
                ['Rina Wulandari', 'P'],
                ['Samsul Arifin', 'L'],
                ['Tiara Kusuma', 'P'],
                ['Ujang Suryana', 'L'],
                ['Vina Melinda', 'P'],
                ['Wahyu Saputra', 'L'],
                ['Yuni Astuti', 'P'],
            ],
            2026 => [
                ['Zainal Abidin', 'L'],
                ['Anisa Fitriani', 'P'],
                ['Bagas Wicaksono', 'L'],
                ['Cahaya Murni', 'P'],
                ['Dedi Kurniawan', 'L'],
                ['Elsa Aprilia', 'P'],
                ['Farhan Maulana', 'L'],
                ['Gusti Ayu', 'P'],
                ['Haris Setiawan', 'L'],
                ['Intan Permatasari', 'P'],
            ],
        ],

        // AKL (major code 3)
        3 => [
            2024 => [
                ['Jihan Aulia', 'P'],
                ['Khoirul Anam', 'L'],
                ['Lintang Sari', 'P'],
                ['Miftahul Huda', 'L'],
                ['Nadia Rahmawati', 'P'],
                ['Oki Setiawan', 'L'],
                ['Pratiwi Kusuma', 'P'],
                ['Rahmat Hidayat', 'L'],
                ['Siska Amelia', 'P'],
                ['Tono Wijaya', 'L'],
            ],
            2025 => [
                ['Ulfa Nuraini', 'P'],
                ['Vicky Prasetyo', 'L'],
                ['Winda Lestari', 'P'],
                ['Yanto Susanto', 'L'],
                ['Zulaikha Putri', 'P'],
                ['Agus Salim', 'L'],
                ['Bella Safira', 'P'],
                ['Catur Wibowo', 'L'],
                ['Dinda Ayu', 'P'],
                ['Erwin Saputra', 'L'],
            ],
            2026 => [
                ['Fina Anggraini', 'P'],
                ['Gilang Ramadhan', 'L'],
                ['Hesti Purwanti', 'P'],
                ['Ilham Nugroho', 'L'],
                ['Jamilah Sari', 'P'],
                ['Kurnia Sandi', 'L'],
                ['Laila Fitriani', 'P'],
                ['Mukhlis Hidayat', 'L'],
                ['Nisa Fauziah', 'P'],
                ['Orin Setiawan', 'L'],
            ],
        ],
    ];

    private const BIRTH_PLACES = [
        'Sidoarjo', 'Surabaya', 'Gresik', 'Malang', 'Mojokerto',
        'Pasuruan', 'Jombang', 'Lamongan', 'Bangkalan', 'Probolinggo',
    ];

    private const STREETS = [
        'Jl. Melati', 'Jl. Mawar', 'Jl. Kenanga', 'Jl. Anggrek', 'Jl. Flamboyan',
        'Jl. Cempaka', 'Jl. Dahlia', 'Jl. Kamboja', 'Jl. Tulip', 'Jl. Sakura',
    ];

    private const VILLAGES = [
        'Sukodono', 'Buduran', 'Candi', 'Taman', 'Waru',
        'Gedangan', 'Sedati', 'Krian', 'Balongbendo', 'Tulangan',
    ];

    private const PARENTS = [
        'Bambang Sutrisno', 'Sri Wahyuni', 'Agus Setiawan', 'Dewi Rahayu',
        'Hadi Purnomo', 'Siti Maryam', 'Joko Wibowo', 'Endang Lestari',
        'Rudi Hartono', 'Tutik Handayani', 'Slamet Riyadi', 'Nur Hidayah',
        'Imam Syafii', 'Murni Astuti', 'Yusuf Maulana', 'Rahmawati',
        'Darto Santoso', 'Wiwik Suryani', 'Hasan Basri', 'Lailatul Badriyah',
        'Sugeng Riyanto', 'Anik Puspitasari', 'Mustofa Hadi', 'Kusuma Wardani',
        'Eko Yulianto', 'Retno Palupi', 'Zainal Arifin', 'Fatimah Zahra',
        'Hendro Susilo', 'Yuli Kartika', 'Bambang Iriyanto', 'Nurul Hidayati',
    ];

    public function run(): void
    {
        $this->command->info('Seeding: tahun ajaran, jurusan, kelas, dan tarif SPP...');

        // 1. Academic year
        $tahunAjaran = TahunAjaran::firstOrCreate(
            ['tahun_ajaran' => self::TAHUN_AJARAN],
            [
                'tanggal_mulai'   => '2026-07-14',
                'tanggal_selesai' => '2027-06-30',
                'status'          => 'aktif',
            ]
        );

        // 2. Majors (RPL, TKJ, AKL)
        $jurusanMap = [];
        foreach (self::MAJORS as $code => [$kode, $nama]) {
            $jurusanMap[$code] = Jurusan::firstOrCreate(
                ['kode_jurusan' => $kode],
                ['nama_jurusan' => $nama, 'keterangan' => 'Program Keahlian ' . $nama]
            );
        }

        // 3. One class per grade and major, e.g. "XII RPL 1"
        $kelasMap = [];
        foreach (self::COHORTS as $grade) {
            foreach (self::MAJORS as $code => [$kode]) {
                $namaKelas = $grade . ' ' . $kode . ' 1';

                $kelasMap["{$grade}-{$code}"] = Kelas::firstOrCreate(
                    [
                        'nama_kelas'      => $namaKelas,
                        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                    ],
                    [
                        'tingkat'    => $grade,
                        'id_jurusan' => $jurusanMap[$code]->id_jurusan,
                    ]
                );
            }
        }

        // 4. Tuition rate for the active academic year
        $tarif = TarifSpp::firstOrCreate(
            [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                'nominal'         => self::TARIF_NOMINAL,
            ],
            [
                'keterangan' => 'SPP Reguler TA ' . self::TAHUN_AJARAN,
                'status'     => 'aktif',
            ]
        );

        $this->command->info('Seeding: siswa, akun login, dan virtual account...');

        // 5. Students, their login accounts, and their virtual accounts
        $usedNis  = [];
        $usedNisn = [];
        $index    = 0;

        foreach (self::STUDENTS as $majorCode => $cohorts) {
            foreach ($cohorts as $enrollmentYear => $students) {
                $grade = self::COHORTS[$enrollmentYear];
                $kelas = $kelasMap["{$grade}-{$majorCode}"];

                $sequence = 0;
                foreach ($students as [$nama, $jenisKelamin]) {
                    $sequence++;
                    $index++;

                    $nis  = $this->makeNis($enrollmentYear, $majorCode, $sequence);
                    $nisn = $this->makeNisn($enrollmentYear, $majorCode, $sequence);

                    if (isset($usedNis[$nis])) {
                        throw new RuntimeException("Duplicate NIS generated: {$nis}");
                    }
                    if (isset($usedNisn[$nisn])) {
                        throw new RuntimeException("Duplicate NISN generated: {$nisn}");
                    }

                    $usedNis[$nis]    = true;
                    $usedNisn[$nisn]  = true;

                    if (Siswa::where('nis', $nis)->exists()) {
                        $this->command->line("  Skip existing NIS {$nis}");
                        continue;
                    }

                    // Login account uses the NIS as both username and password.
                    $user = User::firstOrCreate(
                        ['username' => $nis],
                        [
                            'nama'     => $nama,
                            'password' => $nis,
                            'status'   => 'aktif',
                        ]
                    );

                    if (! $user->hasRole('siswa')) {
                        $user->assignRole('siswa');
                    }

                    $siswa = Siswa::create([
                        'nis'             => $nis,
                        'nisn'            => $nisn,
                        'nama'            => $nama,
                        'jenis_kelamin'   => $jenisKelamin,
                        'tempat_lahir'    => self::BIRTH_PLACES[($index - 1) % count(self::BIRTH_PLACES)],
                        'tanggal_lahir'   => $this->makeBirthDate($enrollmentYear, $majorCode, $sequence),
                        'alamat'          => $this->makeAddress($index),
                        'no_hp'           => $this->makePhoneNumber($index),
                        'nama_orang_tua'  => self::PARENTS[($index * 3 + $majorCode) % count(self::PARENTS)],
                        'id_kelas'        => $kelas->id_kelas,
                        'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                        'id_user'         => $user->id,
                        'status'          => 'aktif',
                    ]);

                    VirtualAccount::firstOrCreate(
                        ['id_siswa' => $siswa->id_siswa],
                        [
                            'nomor_va'       => self::VA_PREFIX . $nis,
                            'prefix'         => self::VA_PREFIX,
                            'status'         => 'aktif',
                            'tanggal_dibuat' => now()->toDateString(),
                        ]
                    );

                    $this->command->line("  {$nis} | {$nama} | {$grade} {$this->majorName($majorCode)} 1");
                }
            }
        }

        // 6. Monthly SPP invoices for July-October 2026
        //    July and August are already paid; September is the live demo month.
        $this->command->info('Seeding: tagihan SPP (Juli-Oktober 2026)...');

        $months = [
            7  => 'PAID',
            8  => 'PAID',
            9  => 'UNPAID',
            10 => 'UNPAID',
        ];

        $allSiswa = Siswa::where('status', 'aktif')->get();

        foreach ($allSiswa as $siswa) {
            foreach ($months as $bulan => $status) {
                TagihanSpp::firstOrCreate(
                    [
                        'id_siswa' => $siswa->id_siswa,
                        'bulan'    => $bulan,
                        'tahun'    => 2026,
                    ],
                    [
                        'id_tarif'            => $tarif->id_tarif,
                        'id_tahun_ajaran'     => $tahunAjaran->id_tahun_ajaran,
                        'nominal'             => self::TARIF_NOMINAL,
                        'tanggal_jatuh_tempo' => Carbon::createFromDate(2026, $bulan, 1)->endOfMonth()->toDateString(),
                        'status'              => $status,
                    ]
                );
            }
        }

        $this->command->info('DemoDataSeeder complete.');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Tahun Ajaran',     TahunAjaran::count()],
                ['Jurusan',          Jurusan::count()],
                ['Kelas',            Kelas::count()],
                ['Tarif SPP',        TarifSpp::count()],
                ['Siswa',            Siswa::count()],
                ['Virtual Account',  VirtualAccount::count()],
                ['Tagihan SPP',      TagihanSpp::count()],
            ]
        );
    }

    /**
     * Build a five-digit NIS in YYJNN format.
     */
    private function makeNis(int $enrollmentYear, int $majorCode, int $sequence): string
    {
        $nis = substr((string) $enrollmentYear, -2)
            . $majorCode
            . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);

        if (! preg_match('/^\d{5}$/', $nis)) {
            throw new RuntimeException("Invalid NIS generated: {$nis}");
        }

        return $nis;
    }

    /**
     * Build a unique ten-digit NISN, e.g. 0020241001.
     */
    private function makeNisn(int $enrollmentYear, int $majorCode, int $sequence): string
    {
        return '00'
            . $enrollmentYear
            . $majorCode
            . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Students are 16 years old when they enroll.
     */
    private function makeBirthDate(int $enrollmentYear, int $majorCode, int $sequence): string
    {
        $year  = $enrollmentYear - 16;
        $month = (($sequence + $majorCode - 1) % 12) + 1;
        $day   = ((($enrollmentYear % 10) * 3 + $sequence * 2 + $majorCode) % 28) + 1;

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    private function makeAddress(int $index): string
    {
        $street  = self::STREETS[($index - 1) % count(self::STREETS)];
        $village = self::VILLAGES[($index - 1) % count(self::VILLAGES)];
        $number  = (($index - 1) * 7) % 90 + 1;

        return "{$street} No. {$number}, Desa {$village}, Sidoarjo";
    }

    private function makePhoneNumber(int $index): string
    {
        return '0812' . str_pad((string) (10_000_000 + $index * 137), 8, '0', STR_PAD_LEFT);
    }

    private function majorName(int $majorCode): string
    {
        return self::MAJORS[$majorCode][0];
    }
}
