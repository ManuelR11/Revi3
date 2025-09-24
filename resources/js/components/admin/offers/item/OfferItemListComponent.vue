<template>
  <OfferItemCreateComponent :props="offerProps" />
  <br><br>

  <!-- Total de unidades del combo (opcional) -->
  <div class="mb-2" v-if="offerItems.length">
    <strong>{{ $t('label.total') }}:</strong> {{ totalUnits }}
  </div>

  <div class="db-card" v-if="offerItems.length > 0">
    <div class="db-table-responsive">
      <table class="db-table stripe">
        <thead class="db-table-head">
          <tr class="db-table-head-tr">
            <th class="db-table-head-th">{{ $t("label.name") }}</th>
            <th class="db-table-head-th">{{ $t("label.price") }}</th>
            <th class="db-table-head-th">{{ $t("label.quantity") }}</th> <!-- 👈 NUEVA COLUMNA -->
            <th class="db-table-head-th">{{ $t("label.status") }}</th>
            <th class="db-table-head-th">{{ $t("label.action") }}</th>
          </tr>
        </thead>
        <tbody class="db-table-body">
          <tr class="db-table-body-tr" v-for="offerItem in offerItems" :key="offerItem.id">
            <td class="db-table-body-td">
              {{ offerItem.offer_item_name }}
            </td>
            <td class="db-table-body-td">
              {{ offerItem.offer_item_flat_price }}
            </td>
            <td class="db-table-body-td">
              {{ offerItem.quantity ?? 1 }} <!-- 👈 MUESTRA CANTIDAD -->
            </td>
            <td class="db-table-body-td">
              <span :class="statusClass(offerItem.offer_item_status)">
                {{ enums.statusEnumArray[offerItem.offer_item_status] }}
              </span>
            </td>
            <td class="db-table-body-td">
              <SmIconDeleteComponent @click="destroy(offerItem.id)" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="p-4 text-center" v-else>
    <div class="max-w-[300px] mx-auto mt-2">
      <img class="w-full h-full" :src="ENV.API_URL + '/images/default/not-found.png'" alt="Not Found">
    </div>
    <span class="d-block mt-3 text-lg">{{ $t('message.no_data_available') }}</span>
  </div>
</template>

<script>
import SmSidebarModalCreateComponent from "../../components/buttons/SmSidebarModalCreateComponent";
import alertService from "../../../../services/alertService";
import statusEnum from "../../../../enums/modules/statusEnum";
import appService from "../../../../services/appService";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent";
import SmIconModalEditComponent from "../../components/buttons/SmIconModalEditComponent";
import OfferItemCreateComponent from "./OfferItemCreateComponent";
import ENV from "../../../../config/env";

export default {
  name: "OfferItemListComponent",
  components: {
    OfferItemCreateComponent, SmSidebarModalCreateComponent, SmIconModalEditComponent, SmIconDeleteComponent
  },
  props: { offer: { type: Number } },
  data() {
    return {
      loading: { isActive: false },
      enums: {
        statusEnum,
        statusEnumArray: {
          [statusEnum.ACTIVE]: this.$t("label.active"),
          [statusEnum.INACTIVE]: this.$t("label.inactive"),
        },
      },
      offerProps: {
        id: this.offer,
        form: {
          item_id: null,
          quantity: 1, // 👈 default para el modal
        },
        search: {
          id: this.offer,
          paginate: 0,
          order_column: 'id',
          order_type: 'desc',
        }
      },
      ENV
    }
  },
  mounted() {
    this.list();
  },
  computed: {
    offerItems() {
      return this.$store.getters['offerItem/lists'];
    },
    // 👇 Suma de cantidades (opcional para mostrar total)
    totalUnits() {
      return this.offerItems.reduce((acc, oi) => acc + (Number(oi.quantity ?? 1)), 0);
    }
  },
  methods: {
    statusClass(status) {
      return appService.statusClass(status);
    },
    list() {
      this.loading.isActive = true;
      this.$store.dispatch("offerItem/lists", this.offerProps.search)
        .finally(() => { this.loading.isActive = false; });
    },
    destroy(id) {
      appService.destroyConfirmation().then(() => {
        this.loading.isActive = true;
        this.$store.dispatch('offerItem/destroy', { offer: this.offer, id, search: this.offerProps.search })
          .then(() => {
            this.loading.isActive = false;
            alertService.successFlip(null, this.$t('label.item'));
          })
          .catch((err) => {
            this.loading.isActive = false;
            alertService.error(err.response.data.message);
          });
      });
    }
  }
}
</script>