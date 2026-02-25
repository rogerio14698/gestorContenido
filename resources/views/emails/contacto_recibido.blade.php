<h2>Nuevo mensaje desde la web<h2>
<ul>
    <li><strong>Nombre y apellidos:</strong> {{ $data['nombre'] }}</li>
    <li><strong>Email:</strong> {{ $data['email'] }}</li>
    <li><strong>Teléfono:</strong> {{ $data['telefono'] ?? '-' }}</li>
    <li><strong>Mensaje:</strong><br>{{ $data['mensaje'] }}</li>
</ul>