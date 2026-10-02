<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\LaporanBulanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesFixtures;
use Tests\TestCase;

class KetuaFaqApiTest extends TestCase
{
    use CreatesFixtures, RefreshDatabase;

    public function test_ketua_can_manage_faq_only_for_their_own_ekskul(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $otherEkskul = $this->makeEkskul();
        $foreignFaq = $otherEkskul->faqs()->create([
            'pertanyaan' => 'Pertanyaan ekskul lain?',
            'status' => Faq::STATUS_PENDING,
        ]);

        $this->actingAs($ketua['user'])
            ->postJson('/api/ketua/faq', [
                'pertanyaan' => 'Apa yang perlu dibawa?',
                'jawaban' => 'Pakaian latihan.',
            ])
            ->assertCreated();

        $this->actingAs($ketua['user'])
            ->postJson("/api/ketua/faq/{$foreignFaq->id}/answer", [
                'jawaban' => 'Tidak boleh mengubah FAQ ini.',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('faqs', [
            'ekskul_id' => $ekskul->id,
            'pertanyaan' => 'Apa yang perlu dibawa?',
            'status' => Faq::STATUS_ANSWERED,
        ]);
    }

    public function test_ketua_can_download_only_their_own_reports_and_attendance_as_pdfs(): void
    {
        $ekskul = $this->makeEkskul();
        $ketua = $this->makeKetua($ekskul);
        $report = LaporanBulanan::create([
            'ekskul_id' => $ekskul->id,
            'bulan' => now()->format('Y-m'),
            'materi_kegiatan' => 'Latihan rutin',
            'status' => LaporanBulanan::STATUS_DRAFT,
        ]);
        $foreignReport = LaporanBulanan::create([
            'ekskul_id' => $this->makeEkskul()->id,
            'bulan' => now()->format('Y-m'),
            'materi_kegiatan' => 'Laporan milik ekskul lain',
            'status' => LaporanBulanan::STATUS_DRAFT,
        ]);

        $this->actingAs($ketua['user'])
            ->get("/api/ketua/laporan-bulanan/{$report->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($ketua['user'])
            ->getJson("/api/ketua/laporan-bulanan/{$foreignReport->id}/pdf")
            ->assertForbidden();

        $this->actingAs($ketua['user'])
            ->get('/api/ketua/rekap/pdf?bulan='.now()->format('Y-m'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
