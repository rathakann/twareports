<?php

namespace Tests\Feature;

use App\Models\MasterData\Branch;
use App\Models\MasterData\Document;
use App\Models\MasterData\DocumentSeries;
use App\Models\MasterData\Item;
use App\Models\MasterData\Uom;
use App\Models\MasterData\UomGroup;
use App\Models\User;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(MasterDataSeeder::class);

        $this->superAdmin = User::where('email', 'superadmin@twareports.test')->first();
    }

    /**
     * Test guests are redirected to login for all master data routes.
     */
    public function test_guests_are_redirected_to_login_for_master_data(): void
    {
        $routes = [
            'master-data.branches.index',
            'master-data.warehouses.index',
            'master-data.bins.index',
            'master-data.business-partners.index',
            'master-data.items.index',
            'master-data.uoms.index',
            'master-data.uom-groups.index',
            'master-data.item-groups.index',
            'master-data.item-classes.index',
            'master-data.barcodes.index',
            'master-data.boms.index',
            'master-data.documents.index',
            'master-data.document-series.index',
        ];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertRedirect(route('login'));
        }
    }

    /**
     * Test Master Data group and all menus are rendered in navigation.
     */
    public function test_navigation_group_master_data_is_rendered(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Master Data');
        $response->assertSee('Branches');
        $response->assertSee('Warehouses');
        $response->assertSee('Bins');
        $response->assertSee('Business Partner');
        $response->assertSee('Items');
        $response->assertSee('UoM');
        $response->assertSee('UoM Group');
        $response->assertSee('Item Group');
        $response->assertSee('Item Class');
        $response->assertSee('Barcode');
        $response->assertSee('Bom');
        $response->assertSee('Documents');
        $response->assertSee('Document Series');
        $response->assertSee('horizontal-master-data-dropdown');
    }

    /**
     * Test Super Admin can access all 13 master data index views.
     */
    public function test_super_admin_can_view_all_master_data_pages(): void
    {
        $routes = [
            'master-data.branches.index',
            'master-data.warehouses.index',
            'master-data.bins.index',
            'master-data.business-partners.index',
            'master-data.items.index',
            'master-data.uoms.index',
            'master-data.uom-groups.index',
            'master-data.item-groups.index',
            'master-data.item-classes.index',
            'master-data.barcodes.index',
            'master-data.boms.index',
            'master-data.documents.index',
            'master-data.document-series.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->superAdmin)->get(route($route));
            $response->assertOk();
            $response->assertSee('Sync');
            $response->assertSee('Last sync date:');
        }
    }

    /**
     * Test DataTables AJAX requests return valid records JSON.
     */
    public function test_master_data_datatables_ajax_returns_json_records(): void
    {
        $routes = [
            'master-data.branches.index',
            'master-data.warehouses.index',
            'master-data.bins.index',
            'master-data.business-partners.index',
            'master-data.items.index',
            'master-data.uoms.index',
            'master-data.uom-groups.index',
            'master-data.item-groups.index',
            'master-data.item-classes.index',
            'master-data.barcodes.index',
            'master-data.boms.index',
            'master-data.documents.index',
            'master-data.document-series.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->superAdmin)
                ->getJson(route($route, ['draw' => 1]), [
                    'X-Requested-With' => 'XMLHttpRequest',
                ]);

            $response->assertOk();
            $response->assertJsonStructure([
                'draw',
                'recordsTotal',
                'recordsFiltered',
                'data',
            ]);

            $this->assertGreaterThan(0, $response->json('recordsTotal'));
        }
    }

    /**
     * Test show route returns single item JSON details.
     */
    public function test_master_data_show_returns_json_details(): void
    {
        $branch = Branch::first();
        $this->assertNotNull($branch);

        $response = $this->actingAs($this->superAdmin)->getJson(route('master-data.branches.show', $branch));
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'bpl_id' => $branch->bpl_id,
                'branch_name' => $branch->branch_name,
            ],
        ]);

        $uom = Uom::first();
        $this->assertNotNull($uom);
        $responseUom = $this->actingAs($this->superAdmin)->getJson(route('master-data.uoms.show', $uom));
        $responseUom->assertOk();
        $responseUom->assertJson([
            'success' => true,
            'data' => [
                'uom_code' => $uom->uom_code,
            ],
        ]);

        $uomGroup = UomGroup::first();
        $this->assertNotNull($uomGroup);
        $responseUomGroup = $this->actingAs($this->superAdmin)->getJson(route('master-data.uom-groups.show', $uomGroup));
        $responseUomGroup->assertOk();
        $responseUomGroup->assertJson([
            'success' => true,
            'data' => [
                'ugp_code' => $uomGroup->ugp_code,
            ],
        ]);

        $doc = Document::first();
        $this->assertNotNull($doc);
        $responseDoc = $this->actingAs($this->superAdmin)->getJson(route('master-data.documents.show', $doc));
        $responseDoc->assertOk();
        $responseDoc->assertJson([
            'success' => true,
            'data' => [
                'object_code' => $doc->object_code,
            ],
        ]);

        $series = DocumentSeries::first();
        $this->assertNotNull($series);
        $responseSeries = $this->actingAs($this->superAdmin)->getJson(route('master-data.document-series.show', $series));
        $responseSeries->assertOk();
        $responseSeries->assertJson([
            'success' => true,
            'data' => [
                'series_code' => $series->series_code,
            ],
        ]);

        $item = Item::first();
        $this->assertNotNull($item);
        $responseItem = $this->actingAs($this->superAdmin)->getJson(route('master-data.items.show', $item));
        $responseItem->assertOk();
        $responseItem->assertJson([
            'success' => true,
            'data' => [
                'item_code' => $item->item_code,
            ],
        ]);
    }

    /**
     * Test Document Series can be filtered by fiscal period and branch.
     */
    public function test_document_series_filters_by_period_and_branch(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('master-data.document-series.index'));
        $response->assertOk();
        $response->assertSee('Fiscal Period');
        $response->assertSee('Branch');

        // Test filtering by period_indicator
        $responseFilteredPeriod = $this->actingAs($this->superAdmin)
            ->getJson(route('master-data.document-series.index', [
                'draw' => 1,
                'period_indicator' => '2025',
            ]), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $responseFilteredPeriod->assertOk();
        $data = $responseFilteredPeriod->json('data');
        $this->assertNotEmpty($data);
        foreach ($data as $item) {
            $this->assertEquals('2025', $item['period_indicator']);
        }

        // Test filtering by branch_code
        $responseFilteredBranch = $this->actingAs($this->superAdmin)
            ->getJson(route('master-data.document-series.index', [
                'draw' => 1,
                'branch_code' => 'Head Office (BPL01)',
            ]), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $responseFilteredBranch->assertOk();
        $branchData = $responseFilteredBranch->json('data');
        $this->assertNotEmpty($branchData);
        foreach ($branchData as $item) {
            $this->assertEquals('Head Office (BPL01)', $item['branch_code']);
        }
    }
}
