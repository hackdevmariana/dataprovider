<?php

namespace App\Console\Commands;

use App\Models\ContentHashtag;
use Illuminate\Console\Command;

class TestContentHashtagResource extends Command
{
    protected $signature = 'test:content-hashtag-resource';
    protected $description = 'Test the ContentHashtag resource';

    public function handle()
    {
        $this->info('🧪 Probando el Resource de ContentHashtag...');
        
        $count = ContentHashtag::count();
        $this->info("📊 Total de registros: {$count}");
        
        if ($count === 0) {
            $this->error('❌ No hay registros de ContentHashtag');
            return 1;
        }
        
        $sample = ContentHashtag::with(['hashtag', 'addedBy', 'hashtaggable'])->first();
        $this->info("🔍 Registro de ejemplo:");
        $this->info("   • ID: {$sample->id}");
        $this->info("   • Hashtag: " . ($sample->hashtag->name ?? 'Sin hashtag'));
        $this->info("   • Usuario: " . ($sample->addedBy->name ?? 'Sin usuario'));
        $this->info("   • Tipo: " . ($sample->hashtaggable_type ?? 'Sin tipo'));
        $this->info("   • Contenido: " . ($sample->hashtaggable ? 'Cargado' : 'No cargado'));
        $this->info("   • Clicks: " . ($sample->clicks_count ?? 0));
        $this->info("   • Relevancia: " . ($sample->relevance_score ?? 0));
        $this->info("   • Auto-generado: " . ($sample->is_auto_generated ? 'Sí' : 'No'));
        $this->info("   • Confianza IA: " . ($sample->confidence_score ?? 'N/A'));
        
        $this->info("\n✅ Resource de ContentHashtag funcionando correctamente");
        return 0;
    }
}
