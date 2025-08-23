<div class="space-y-6">
    @if($request)
        <!-- Información de la Instalación -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">📊 Información de la Instalación</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Potencia de Instalación:</span>
                    <p class="text-lg font-semibold text-blue-600">{{ $request->getFormattedPower() }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Período de Cálculo:</span>
                    <p class="text-lg font-semibold text-green-600">{{ $request->getPeriodLabel() }}</p>
                </div>
            </div>
        </div>

        <!-- Cálculos de Producción -->
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-900 mb-3">⚡ Cálculos de Producción</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-blue-700">Producción Específica:</span>
                    <p class="text-lg font-semibold text-blue-800">
                        {{ $request->production_kwh ? number_format($request->production_kwh, 2) . ' kWh' : 'No especificada' }}
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-blue-700">Producción Estimada:</span>
                    <p class="text-lg font-semibold text-blue-800">{{ $request->getFormattedProduction() }}</p>
                </div>
            </div>
            
            @if(!$request->production_kwh)
                <div class="mt-3 p-3 bg-blue-100 rounded">
                    <p class="text-sm text-blue-800">
                        <strong>Fórmula de cálculo:</strong><br>
                        Potencia × Horas del período × Eficiencia × (1 - Pérdidas)
                    </p>
                    <p class="text-sm text-blue-700 mt-2">
                        {{ $request->installation_power_kw }} kW × 
                        {{ $request->getHoursInPeriod() }} horas × 
                        {{ $request->efficiency_ratio ? number_format($request->efficiency_ratio, 4) : '1.0000' }} × 
                        (1 - {{ $request->loss_factor ? number_format($request->loss_factor, 4) : '0.0000' }})
                    </p>
                </div>
            @endif
        </div>

        <!-- Factores de Eficiencia -->
        <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-green-900 mb-3">🔧 Factores de Eficiencia</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-green-700">Ratio de Eficiencia:</span>
                    <p class="text-lg font-semibold text-green-800">{{ $request->getFormattedEfficiencyRatio() }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-green-700">Factor de Pérdidas:</span>
                    <p class="text-lg font-semibold text-green-800">{{ $request->getFormattedLossFactor() }}</p>
                </div>
            </div>
        </div>

        <!-- Cálculo de Ahorro de CO2 -->
        <div class="bg-emerald-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-emerald-900 mb-3">🌱 Ahorro de Carbono</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-emerald-700">Producción Total:</span>
                    <p class="text-lg font-semibold text-emerald-800">{{ $request->getFormattedProduction() }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-emerald-700">Ahorro de CO2:</span>
                    <p class="text-lg font-semibold text-emerald-800">{{ $request->getFormattedCarbonSavings() }}</p>
                </div>
            </div>
            
            <div class="mt-3 p-3 bg-emerald-100 rounded">
                <p class="text-sm text-emerald-800">
                    <strong>Factor de emisión utilizado:</strong> 0.275 kg CO2/kWh (promedio de la red eléctrica española)
                </p>
                <p class="text-sm text-emerald-700 mt-2">
                    <strong>Fórmula:</strong> Producción × Factor de emisión
                </p>
            </div>
        </div>

        <!-- Información Regional -->
        <div class="bg-purple-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-purple-900 mb-3">📍 Información Regional</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-sm font-medium text-purple-700">Provincia:</span>
                    <p class="text-lg font-semibold text-purple-800">
                        {{ $request->province?->name ?? 'No especificada' }}
                    </p>
                </div>
                <div>
                    <span class="text-sm font-medium text-purple-700">Municipio:</span>
                    <p class="text-lg font-semibold text-purple-800">
                        {{ $request->municipality?->name ?? 'No especificado' }}
                    </p>
                </div>
            </div>
            
            @if($request->province || $request->municipality)
                <div class="mt-3 p-3 bg-purple-100 rounded">
                    <p class="text-sm text-purple-800">
                        <strong>Ubicación completa:</strong> {{ $request->getRegionalInfo() }}
                    </p>
                    <p class="text-sm text-purple-700 mt-2">
                        Los factores regionales se aplican para cálculos más precisos.
                    </p>
                </div>
            @else
                <div class="mt-3 p-3 bg-gray-100 rounded">
                    <p class="text-sm text-gray-700">
                        No se han especificado factores regionales. Se utilizan valores estándar.
                    </p>
                </div>
            @endif
        </div>

        <!-- Fechas del Período -->
        @if($request->start_date || $request->end_date)
            <div class="bg-orange-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-orange-900 mb-3">📅 Período de Cálculo</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm font-medium text-orange-700">Fecha de Inicio:</span>
                        <p class="text-lg font-semibold text-orange-800">
                            {{ $request->start_date ? $request->start_date->format('d/m/Y') : 'No especificada' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-orange-700">Fecha de Fin:</span>
                        <p class="text-lg font-semibold text-orange-800">
                            {{ $request->end_date ? $request->end_date->format('d/m/Y') : 'No especificada' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Resumen Final -->
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">📋 Resumen de la Solicitud</h3>
            <div class="space-y-2">
                <p class="text-sm text-gray-700">
                    <strong>ID:</strong> {{ $request->id }}
                </p>
                <p class="text-sm text-gray-700">
                    <strong>Creada:</strong> {{ $request->created_at->format('d/m/Y H:i') }}
                </p>
                <p class="text-sm text-gray-700">
                    <strong>Última actualización:</strong> {{ $request->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

    @else
        <div class="text-center py-8">
            <p class="text-gray-500">No se pudo cargar la información de la solicitud.</p>
        </div>
    @endif
</div>
