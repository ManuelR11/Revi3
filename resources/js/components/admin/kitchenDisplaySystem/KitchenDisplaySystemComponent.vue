<template>
  <LoadingComponent :props="loading" />
  <div class="row md:mt-4 lg:mt-0">
    <!-- Ocultamos los tabs móviles; mostramos TODO de una vez -->
    <div class="lg:hidden flex items-center w-full px-4 hidden">
      <button
        class="kitchen-board db-tab-btn active text-base text-black font-semibold h-[38px] bg-white flex items-center justify-center rounded-l-lg px-7"
        data-tab="#item-order"
      >
        {{ $t('label.items_board') }}
      </button>
      <button
        class="kitchen-board db-tab-btn text-base text-black font-semibold h-[38px] bg-white flex items-center justify-center ro rounded-r-lg px-7"
        data-tab="#today-order"
      >
        {{ $t('label.todays_order') }}
      </button>
    </div>

    <!-- ITEMS BOARD siempre visible -->
    <div id="item-order" class="col-12 lg:col-3 db-tab-div block">
      <div class="db-card rounded-[10px] w-full">
        <div class="h-screen md:h-[calc(100vh-127px)] overflow-hidden">
          <div class="p-3 pb-2 border-b border-[#D9DBE9]">
            <h3 class="text-lg font-semibold">{{ $t('label.items_board') }}</h3>
          </div>
          <ul class="h-full thin-scrolling overflow-auto pb-12">
            <li
              v-for="(orderItem, idx) in orderItems"
              :key="`item-board-${idx}-${orderItem.item_id ?? idx}`"
              class="px-3 py-2 flex items-start justify-between gap-2 border-b border-[#EFF0F6] last:border-none"
            >
              <div>
                <h5 class="text-sm font-medium mb-1">{{ orderItem.item_name }}</h5>

                <p v-if="orderItem.item_variations?.length" class="text-xs font-normal font-client capitalize text-[#6E7191]">
                  <span
                    v-for="(variation, index) in orderItem.item_variations"
                    :key="`var-${orderItem.item_id}-${index}`"
                    class="text-heading"
                  >
                    {{ variation.variation_name }}: {{ variation.name
                    }}<span v-if="index + 1 < orderItem.item_variations.length">,&nbsp;</span>
                  </span>
                </p>

                <div class="flex gap-1" v-if="orderItem.item_extras?.length">
                  <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{ $t('label.extras') }}:</h3>
                  <p class="text-xs font-normal font-client capitalize text-[#6E7191]">
                    <span
                      v-for="(extra, index) in orderItem.item_extras"
                      :key="`extra-${orderItem.item_id}-${index}`"
                      class="text-heading"
                    >
                      {{ extra.name }}<span v-if="index + 1 < orderItem.item_extras.length">,&nbsp;</span>
                    </span>
                  </p>
                </div>

                <div class="flex items-start gap-2" v-if="orderItem.combo_items?.length">
                  <span class="capitalize text-xs font-medium text-gray-700 shrink-0">
                    {{ $t('label.combo_items') }}:
                  </span>
                  <ul class="mt-0.5 space-y-1 list-disc pl-6 md:pl-8">
                    <li
                      v-for="(comboItem, index) in orderItem.combo_items"
                      :key="`combo-${orderItem.item_id}-${index}`"
                      class="leading-tight marker:text-gray-300"
                    >
                      <strong class="text-[1rem] md:text-base text-gray-900">
                        {{ comboItem.quantity }}x {{ comboItem.item_name }}
                      </strong>
                    </li>
                  </ul>
                </div>

                <div class="flex gap-1" v-if="orderItem.instruction">
                  <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{ $t('label.instruction') }}:</h3>
                  <p class="text-xs font-normal font-client capitalize text-[#6E7191]">
                    {{ orderItem.instruction }}
                  </p>
                </div>
              </div>

              <div
                class="text-sm font-medium w-6 h-6 rounded-full bg-black text-white flex items-center justify-center"
              >
                {{ orderItem.quantity }}
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- TODAY ORDER siempre visible -->
    <div id="today-order" class="col-12 lg:col-9 db-tab-div block">
      <div class="ordersTab">
        <div class="db-card px-3 py-2.5 mb-4">
          <div class="swiper kitchen-swiper !flex flex-col gap-y-2 xl:flex-row items-start justify-between">
            <Swiper
              dir="ltr"
              :speed="1000"
              slidesPerView="auto"
              :spaceBetween="12"
              :loop="false"
              class="md:grid sm:grid-cols-2 lg:grid-cols-4 gap-y-2 md:w-fit lg:!w-full w-full"
            >
              <SwiperSlide class="!w-fit">
                <button
                  type="button"
                  @click="list()"
                  class="db-btn text-[#1F1F39] w-fit flex items-center justify-center gap-3 h-11 px-6 rounded-lg transition bg-white hover:text-primary border border-[#D9DBE9] hover:bg-primary/5"
                  :class="!props.search.status ? '!bg-primary/5 text-primary' : ''"
                >
                  <span class="capitalize whitespace-nowrap text-sm font-medium">{{ $t('label.all_orders') }}</span>
                </button>
              </SwiperSlide>
              <SwiperSlide class="!w-fit">
                <button
                  type="button"
                  @click="list(enums.orderStatusEnum.ACCEPT)"
                  :class="props.search.status === enums.orderStatusEnum.ACCEPT ? '!bg-primary/5 text-primary' : ''"
                  class="db-btn text-[#1F1F39] w-fit flex items-center justify-center gap-3 h-11 px-6 rounded-lg transition bg-white hover:text-primary border border-[#D9DBE9] hover:bg-primary/5"
                >
                  <span class="capitalize whitespace-nowrap text-sm font-medium">{{ $t('label.confirmed') }}</span>
                </button>
              </SwiperSlide>
              <SwiperSlide class="!w-fit">
                <button
                  type="button"
                  @click="list(enums.orderStatusEnum.PREPARING)"
                  :class="props.search.status === enums.orderStatusEnum.PREPARING ? '!bg-primary/5 text-primary' : ''"
                  class="db-btn text-[#1F1F39] w-fit flex items-center justify-center gap-3 h-11 px-6 rounded-lg transition bg-white hover:text-primary border border-[#D9DBE9] hover:bg-primary/5"
                >
                  <span class="capitalize whitespace-nowrap text-sm font-medium">{{ $t('label.preparing') }}</span>
                </button>
              </SwiperSlide>
              <SwiperSlide class="!w-fit">
                <button
                  type="button"
                  @click="list(enums.orderStatusEnum.PREPARED)"
                  :class="props.search.status === enums.orderStatusEnum.PREPARED ? '!bg-primary/5 text-primary' : ''"
                  class="db-btn text-[#1F1F39] w-fit flex items-center justify-center gap-3 h-11 px-6 rounded-lg transition bg-white hover:text-primary border border-[#D9DBE9] hover:bg-primary/5"
                >
                  <span class="capitalize whitespace-nowrap text-sm font-medium">{{ $t('label.done') }}</span>
                </button>
              </SwiperSlide>
            </Swiper>

            <form
              @submit.prevent="search"
              class="header-search-group group flex items-center justify-center border border-solid gap-2 px-3 xl:!max-w-[305px] w-full h-11 rounded-lg transition border-[#D9DBE9] focus-within:bg-white focus-within:border-primary"
            >
              <i class="lab lab-search-normal lab-font-size-16"></i>
              <input
                type="text"
                v-model="props.search.order_serial_no"
                placeholder="Search Order"
                class="header-search-field w-full h-full text-xs appearance-none placeholder:font-normal placeholder:text-paragraph text-heading"
              />
              <button
                type="button"
                @click.prevent="searchReset"
                class="modal-close lab lab-close-circle-line transition invisible group-focus-within:visible"
              ></button>
            </form>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- DINE-IN -->
          <div class="db-card rounded-[10px] h-fit">
            <div class="p-3 pb-2" :class="dineinOrders.length > 0 ? 'border-b border-[#D9DBE9] mb-2' : ''">
              <h3 class="text-lg font-semibold">{{ $t('label.dinein_orders') }}</h3>
            </div>

            <div v-if="dineinOrders.length > 0" class="p-3" v-for="(dineinOrder, i) in dineinOrders" :key="`din-${i}`">
              <div class="w-full rounded-lg border border-[#EFF0F6]">
                <div class="py-2.5 px-3 w-full rounded-t-lg flex items-center justify-between bg-[#F0F8FF]">
                  <div class="flex items-center gap-1 text-[#0084FF]">
                    <i class="lab lab-processing lab-font-size-16 text-[#0084FF]"></i>
                    <span class="text-sm font-normal">#{{ dineinOrder.order_serial_no }}</span>
                  </div>

                  <span
                    class="py-0.5 px-2 rounded-[4px] text-[10px] font-client leading-4 capitalize text-white"
                    :class="dineinOrder.status === enums.orderStatusEnum.PREPARED
                      ? 'bg-[#2AC769]'
                      : (dineinOrder.status === enums.orderStatusEnum.ACCEPT ? 'bg-primary' : 'bg-[#F6A609]')"
                  >
                    {{
                      dineinOrder.status === enums.orderStatusEnum.PREPARED
                        ? $t('label.done')
                        : (dineinOrder.status === enums.orderStatusEnum.ACCEPT
                            ? $t('label.confirmed')
                            : dineinOrder.status_name)
                    }}
                  </span>
                </div>

                <div class="w-full pt-2 pb-3 px-3">
                  <p class="text-sm font-normal leading-6 font-client capitalize text-[#6E7191]">
                    {{ $t('label.table_no') }}:
                    <span class="text-heading font-medium">{{ dineinOrder.table_name }}</span>
                  </p>
                  <p class="text-sm font-normal leading-6 font-client capitalize text-[#6E7191]">
                    {{ $t('label.token_no') }}:
                    <span class="text-heading font-medium">
                      {{ dineinOrder.token ? dineinOrder.token : $t('label.online') }}
                    </span>
                  </p>

                  <!-- Siempre expandido -->
                  <div class="text-[#6E7191] text-xs font-[300] flex justify-between items-center w-full mb-1">
                    <span>{{ dineinOrder.order_datetime }}</span>
                  </div>

                  <div class="pt-1">
                    <div
                      v-for="(item, j) in dineinOrder.order_items"
                      :key="`din-item-${i}-${j}-${item.item_id ?? j}`"
                      class="flex items-start gap-2 py-3 border-b border-dashed border-[#EFF0F6] last:border-none"
                    >
                      <h4 class="text-sm font-medium">{{ item.quantity }}x</h4>
                      <div>
                        <h5 class="text-sm font-medium mb-1">{{ item.item_name }}</h5>

                        <p
                          v-if="item.item_variations?.length"
                          class="text-xs font-normal font-client capitalize text-[#6E7191]"
                        >
                          <span
                            v-for="(variation, k) in item.item_variations"
                            :key="`din-var-${i}-${j}-${k}`"
                            class="text-heading"
                          >
                            {{ variation.variation_name }}: {{ variation.name
                            }}<span v-if="k + 1 < item.item_variations.length">,&nbsp;</span>
                          </span>
                        </p>

                        <div class="flex gap-1" v-if="item.item_extras?.length">
                          <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{ $t('label.extras') }}:</h3>
                          <p class="text-xs font-normal font-client capitalize text-[#6E7191]">
                            <span
                              v-for="(extra, k) in item.item_extras"
                              :key="`din-extra-${i}-${j}-${k}`"
                              class="text-heading"
                            >
                              {{ extra.name }}<span v-if="k + 1 < item.item_extras.length">,&nbsp;</span>
                            </span>
                          </p>
                        </div>

                        <!-- Sublista combos con tab -->
                        <div class="flex items-start gap-2" v-if="item.combo_items?.length">
                          <span class="capitalize text-xs font-medium text-gray-700 shrink-0">
                            {{ $t('label.combo_items') }}:
                          </span>
                          <ul class="mt-0.5 space-y-1 list-disc pl-6 md:pl-8">
                            <li
                              v-for="(comboItem, k) in item.combo_items"
                              :key="`din-combo-${i}-${j}-${k}`"
                              class="leading-tight marker:text-gray-300"
                            >
                              <strong class="text-[1rem] md:text-base text-gray-900">
                                {{ comboItem.quantity }}x {{ comboItem.item_name }}
                              </strong>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <button
                      v-if="dineinOrder.status === enums.orderStatusEnum.ACCEPT"
                      type="button"
                      @click="orderStatus(dineinOrder.id, enums.orderStatusEnum.PREPARING)"
                      class="rounded-lg w-full h-9 flex justify-center items-center text-sm font-medium bg-primary text-white"
                    >
                      {{ $t('label.start_preparing') }}
                    </button>
                    <button
                      v-if="dineinOrder.status === enums.orderStatusEnum.PREPARING"
                      type="button"
                      @click="orderStatus(dineinOrder.id, enums.orderStatusEnum.PREPARED)"
                      class="rounded-lg w-full h-9 flex justify-center items-center text-sm font-medium bg-[#1AB759] text-white"
                    >
                      {{ $t('label.mark_done') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAKEAWAY -->
          <div class="db-card rounded-[10px] h-fit">
            <div class="p-3 pb-2" :class="takeawayOrders.length > 0 ? 'border-b border-[#D9DBE9] mb-2' : ''">
              <h3 class="text-lg font-semibold">{{ $t('label.takeaway') }}</h3>
            </div>

            <div
              v-if="takeawayOrders.length > 0"
              class="p-3"
              v-for="(takeawayOrder, i) in takeawayOrders"
              :key="`tk-${i}`"
            >
              <div class="w-full rounded-lg border border-[#EFF0F6]">
                <div class="py-2.5 px-3 w-full rounded-t-lg flex items-center justify-between bg-[#FAF4FF]">
                  <div class="flex items-center gap-1 text-[#9837FF]">
                    <i class="lab lab-processing lab-font-size-16 text-[#9837FF]"></i>
                    <span class="text-sm font-normal">#{{ takeawayOrder.order_serial_no }}</span>
                  </div>

                  <span
                    class="py-0.5 px-2 rounded-[4px] text-[10px] font-client leading-4 capitalize text-white"
                    :class="takeawayOrder.status === enums.orderStatusEnum.PREPARED
                      ? 'bg-[#2AC769]'
                      : (takeawayOrder.status === enums.orderStatusEnum.ACCEPT ? 'bg-primary' : 'bg-[#F6A609]')"
                  >
                    {{
                      takeawayOrder.status === enums.orderStatusEnum.PREPARED
                        ? $t('label.done')
                        : (takeawayOrder.status === enums.orderStatusEnum.ACCEPT
                            ? $t('label.confirmed')
                            : takeawayOrder.status_name)
                    }}
                  </span>
                </div>

                <div class="w-full pt-2 pb-3 px-3">
                  <p class="text-sm font-normal leading-6 font-client capitalize text-[#6E7191]">
                    {{ $t('label.token_no') }}:
                    <span class="text-heading font-medium">
                      {{ takeawayOrder.token ? takeawayOrder.token : $t('label.online') }}
                    </span>
                  </p>

                  <!-- Siempre expandido -->
                  <div class="text-[#6E7191] text-xs font-[300] flex justify-between items-center w-full mb-1">
                    <span>{{ takeawayOrder.order_datetime }}</span>
                  </div>

                  <div class="pt-1">
                    <div
                      v-for="(item, j) in takeawayOrder.order_items"
                      :key="`tk-item-${i}-${j}-${item.item_id ?? j}`"
                      class="flex items-start gap-2 py-3 border-b border-dashed border-[#EFF0F6] last:border-none"
                    >
                      <h4 class="text-sm font-medium">{{ item.quantity }}x</h4>
                      <div>
                        <h5 class="text-sm font-medium mb-1">{{ item.item_name }}</h5>

                        <p
                          v-if="item.item_variations?.length"
                          class="text-xs font-normal font-client capitalize text-[#6E7191]"
                        >
                          <span
                            v-for="(variation, k) in item.item_variations"
                            :key="`tk-var-${i}-${j}-${k}`"
                            class="text-heading"
                          >
                            {{ variation.variation_name }}: {{ variation.name
                            }}<span v-if="k + 1 < item.item_variations.length">,&nbsp;</span>
                          </span>
                        </p>

                        <div class="flex gap-1" v-if="item.item_extras?.length">
                          <h3 class="capitalize text-xs w-fit whitespace-nowrap">{{ $t('label.extras') }}:</h3>
                          <p class="text-xs font-normal font-client capitalize text-[#6E7191]">
                            <span
                              v-for="(extra, k) in item.item_extras"
                              :key="`tk-extra-${i}-${j}-${k}`"
                              class="text-heading"
                            >
                              {{ extra.name }}<span v-if="k + 1 < item.item_extras.length">,&nbsp;</span>
                            </span>
                          </p>
                        </div>

                        <!-- Sublista combos con tab -->
                        <div class="flex items-start gap-2" v-if="item.combo_items?.length">
                          <span class="capitalize text-xs font-medium text-gray-700 shrink-0">
                            {{ $t('label.combo_items') }}:
                          </span>
                          <ul class="mt-0.5 space-y-1 list-disc pl-6 md:pl-8">
                            <li
                              v-for="(comboItem, k) in item.combo_items"
                              :key="`tk-combo-${i}-${j}-${k}`"
                              class="leading-tight marker:text-gray-300"
                            >
                              <strong class="text-[1rem] md:text-base text-gray-900">
                                {{ comboItem.quantity }}x {{ comboItem.item_name }}
                              </strong>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>

                    <button
                      v-if="takeawayOrder.status === enums.orderStatusEnum.ACCEPT"
                      type="button"
                      @click="orderStatus(takeawayOrder.id, enums.orderStatusEnum.PREPARING)"
                      class="rounded-lg w-full h-9 flex justify-center items-center text-sm font-medium bg-primary text-white"
                    >
                      {{ $t('label.start_preparing') }}
                    </button>
                    <button
                      v-if="takeawayOrder.status === enums.orderStatusEnum.PREPARING"
                      type="button"
                      @click="orderStatus(takeawayOrder.id, enums.orderStatusEnum.PREPARED)"
                      class="rounded-lg w-full h-9 flex justify-center items-center text-sm font-medium bg-[#1AB759] text-white"
                    >
                      {{ $t('label.mark_done') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div> <!-- /TAKEAWAY -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import statusEnum from "../../../enums/modules/statusEnum";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";
import askEnum from "../../../enums/modules/askEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import { Swiper, SwiperSlide } from "swiper/vue";

export default {
  name: "KitchenDisplaySystemComponent",
  components: {
    LoadingComponent,
    Swiper,
    SwiperSlide,
  },
  data() {
    return {
      loading: { isActive: false },
      props: {
        search: {
          paginate: 0,
          order_column: "id",
          order_by: "desc",
          order_serial_no: "",
          status: "",
        },
      },
      dineinOrders: [],
      takeawayOrders: [],
      enums: {
        statusEnum: statusEnum,
        orderTypeEnum: orderTypeEnum,
        orderStatusEnum: orderStatusEnum,
        askEnum: askEnum,
      },
      autoRefreshInterval: null,
    };
  },
  computed: {
    orders() {
      return this.$store.getters["kitchenDisplaySystemOrder/lists"];
    },
    orderItems() {
      return this.$store.getters["kitchenDisplaySystemOrder/orderItems"];
    },
  },
  mounted() {
    this.closeSidebar();
    this.items();
    this.list();
    this.startAutoRefresh();
  },
  methods: {
    startAutoRefresh() {
      if (this.$route.path.includes("kitchen-display-system")) {
        this.autoRefreshInterval = setInterval(() => {
          this.items();
          this.list(this.props.search.status);
        }, 30000);
      }
    },
    stopAutoRefresh() {
      if (this.autoRefreshInterval) {
        clearInterval(this.autoRefreshInterval);
        this.autoRefreshInterval = null;
      }
    },
    list(status = "") {
      this.props.search.status = status || "";
      this.loading.isActive = true;
      this.$store
        .dispatch("kitchenDisplaySystemOrder/lists", this.props.search)
        .then((res) => {
          this.dineinOrders = res.data.data.filter(
            (item) => item.order_type === orderTypeEnum.DINING_TABLE
          );
          this.takeawayOrders = res.data.data.filter(
            (item) => item.order_type === orderTypeEnum.TAKEAWAY
          );
          this.loading.isActive = false;
        })
        .catch(() => {
          this.loading.isActive = false;
        });
    },
    items() {
      this.loading.isActive = true;
      this.$store
        .dispatch("kitchenDisplaySystemOrder/orderItems")
        .then(() => {
          this.loading.isActive = false;
        })
        .catch(() => {
          this.loading.isActive = false;
        });
    },
    openSidebar() {
      document?.querySelector(".db-main")?.classList?.remove("expand");
      document?.querySelector(".db-sidebar")?.classList?.remove("active");
      const activeMenu = document.querySelector(".db-sidebar-nav-item.active");
      if (activeMenu) activeMenu.classList.remove("active");
      document?.querySelector(".db-sidebar-nav-menu")?.parentElement?.classList?.add("active");
    },
    closeSidebar() {
      document?.querySelector(".db-main")?.classList?.add("expand");
      document?.querySelector(".db-sidebar")?.classList?.add("active");
    },
    search() {
      this.list();
    },
    searchReset() {
      this.props.search.order_serial_no = "";
      this.list();
    },
    orderStatus(id, status) {
      try {
        this.loading.isActive = true;
        this.$store
          .dispatch("kitchenDisplaySystemOrder/changeStatus", { id, status })
          .then(() => {
            this.loading.isActive = false;
            alertService.successFlip(1, this.$t("label.status"));
            this.list();
            this.items();
          })
          .catch((err) => {
            this.loading.isActive = false;
            alertService.error(err.response.data.message);
          });
      } catch (err) {
        this.loading.isActive = false;
        alertService.error(err?.response?.data?.message || err?.message || "Error");
      }
    },
  },
  beforeUnmount() {
    this.stopAutoRefresh();
    this.openSidebar();
  },
};
</script>