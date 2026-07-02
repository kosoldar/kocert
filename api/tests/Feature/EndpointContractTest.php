<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Smoke tests that validate every route in api.php routes to an actual
 * controller and returns the expected HTTP contract (auth gate + status family).
 */
class EndpointContractTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nombre'   => 'Admin',
            'apellido' => 'Test',
            'email'    => 'admin@kocert.test',
            'password' => Hash::make('secret'),
            'rol'      => 'admin',
            'activo'   => true,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function auth()
    {
        return $this->actingAs($this->admin, 'sanctum');
    }

    /** Assert route exists (not 404/405) and is behind auth gate (returns 401). */
    private function assertProtected(string $method, string $uri): void
    {
        $response = $this->json($method, $uri);
        $this->assertNotEquals(404, $response->status(), "$method $uri → 404 (route missing)");
        $this->assertNotEquals(405, $response->status(), "$method $uri → 405 (wrong method)");
        $response->assertStatus(401);
    }

    // ── Public routes ─────────────────────────────────────────────────────────

    public function test_login_route_rejects_bad_credentials(): void
    {
        // ValidationException → 422 (not 401) — API raises a validation error
        $this->postJson('/api/login', ['email' => 'x@x.com', 'password' => 'wrong'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_with_valid_credentials_returns_token(): void
    {
        $this->postJson('/api/login', [
            'email'    => $this->admin->email,
            'password' => 'secret',
        ])->assertOk()->assertJsonStructure(['token']);
    }

    public function test_verificacion_publica_con_token_invalido_returns_404(): void
    {
        $this->getJson('/api/verificar/token-inexistente')
            ->assertStatus(404);
    }

    // ── Auth gate: all protected GET routes must return 401 without token ─────

    #[DataProvider('protectedGetRoutes')]
    public function test_protected_route_requires_auth(string $uri): void
    {
        $this->assertProtected('GET', $uri);
    }

    public static function protectedGetRoutes(): array
    {
        return [
            'me'               => ['/api/me'],
            'dashboard stats'  => ['/api/dashboard/stats'],
            'soldadores index' => ['/api/soldadores'],
            'empresas index'   => ['/api/empresas'],
            'certificados idx' => ['/api/certificados'],
            'inspectores idx'  => ['/api/inspectores'],
            'normas index'     => ['/api/normas'],
            'procesos index'   => ['/api/procesos'],
            'materiales idx'   => ['/api/materiales'],
            'usuarios index'   => ['/api/usuarios'],
            'catalogos normas' => ['/api/catalogos/normas'],
            'cat next-numero'  => ['/api/catalogos/next-numero'],
            'cat posiciones'   => ['/api/catalogos/posiciones'],
            'cat check-numero' => ['/api/catalogos/check-numero'],
        ];
    }

    // ── Authenticated: index routes return 200 ────────────────────────────────

    #[DataProvider('authenticatedIndexRoutes')]
    public function test_authenticated_index_route_returns_ok(string $uri): void
    {
        $this->auth()->getJson($uri)->assertOk();
    }

    public static function authenticatedIndexRoutes(): array
    {
        return [
            'me'               => ['/api/me'],
            'dashboard stats'  => ['/api/dashboard/stats'],
            'soldadores index' => ['/api/soldadores'],
            'empresas index'   => ['/api/empresas'],
            'certificados idx' => ['/api/certificados'],
            'inspectores idx'  => ['/api/inspectores'],
            'normas index'     => ['/api/normas'],
            'procesos index'   => ['/api/procesos'],
            'materiales idx'   => ['/api/materiales'],
            'usuarios index'   => ['/api/usuarios'],
            'catalogos normas' => ['/api/catalogos/normas'],
            'cat posiciones'   => ['/api/catalogos/posiciones'],
        ];
    }

    // ── Authenticated: show non-existent resource → 404 ──────────────────────

    #[DataProvider('resourceShowRoutes')]
    public function test_show_missing_resource_returns_404(string $uri): void
    {
        $this->auth()->getJson($uri)->assertStatus(404);
    }

    public static function resourceShowRoutes(): array
    {
        return [
            'soldador show'    => ['/api/soldadores/99999'],
            'empresa show'     => ['/api/empresas/99999'],
            'certificado show' => ['/api/certificados/99999'],
            'inspector show'   => ['/api/inspectores/99999'],
            'norma show'       => ['/api/normas/99999'],
            'proceso show'     => ['/api/procesos/99999'],
            'material show'    => ['/api/materiales/99999'],
            'usuario show'     => ['/api/usuarios/99999'],
        ];
    }

    // ── Authenticated: POST empty body → 422 validation, not 500 ─────────────

    #[DataProvider('resourcePostRoutes')]
    public function test_post_empty_body_returns_validation_error(string $uri): void
    {
        $this->auth()->postJson($uri, [])->assertStatus(422);
    }

    public static function resourcePostRoutes(): array
    {
        return [
            'soldadores store'   => ['/api/soldadores'],
            'empresas store'     => ['/api/empresas'],
            'certificados store' => ['/api/certificados'],
            'inspectores store'  => ['/api/inspectores'],
            'normas store'       => ['/api/normas'],
            'procesos store'     => ['/api/procesos'],
            'materiales store'   => ['/api/materiales'],
            'usuarios store'     => ['/api/usuarios'],
        ];
    }

    // ── Catalogos norma/{norma} ────────────────────────────────────────────────

    public function test_catalogo_norma_missing_returns_404(): void
    {
        $this->auth()->getJson('/api/catalogos/norma/99999')->assertStatus(404);
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function test_logout_requires_auth(): void
    {
        $this->assertProtected('POST', '/api/logout');
    }

    public function test_logout_with_real_token_returns_ok(): void
    {
        // actingAs() uses TransientToken which doesn't support delete().
        // Use a real Sanctum token to exercise the full logout path.
        $token = $this->admin->createToken('test-session')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();
    }
}
