<template>
	<section class="mb-16 mt-4">
		<div class="container">
			<LoadingComponent :props="loading" />

			<div class="swiper mb-7 menu-swiper" v-if="categories.length > 1">
				<Swiper
					:speed="1000"
					slidesPerView="auto"
					:spaceBetween="16"
					class="menu-slides"
					dir="rtl"
				>
					<SwiperSlide
						class="!w-fit relative"
						v-for="(category, index) in categories"
						:key="category"
						:class="
							category.id === itemProps.search.item_category_id ||
							(category.id === 0 && itemProps.search.item_category_id === '')
								? 'pos-group'
								: ''
						"
					>
						<router-link
							v-if="index === 0"
							to=""
							@click.prevent="allCategory(category)"
							class="w-32 flex flex-col items-center text-center gap-4 p-3 rounded-2xl border-b-2 border-transparent transition hover:bg-primary-light bg-[#F7F7FC] overflow-hidden"
						>
							<img
								class="h-10 drop-shadow-category"
								:src="category.thumb"
								alt="category"
							/>
							<h3
								class="w-full text-xs leading-[16px] whitespace-nowrap overflow-hidden text-ellipsis font-medium font-rubik"
							>
								{{ category.name }}
							</h3>
						</router-link>
						<router-link
							v-else
							to=""
							@click.prevent="setCategory(category.id, category.slug)"
							class="w-32 flex flex-col items-center text-center gap-4 p-3 rounded-2xl border-b-2 border-transparent transition hover:bg-primary-light bg-[#F7F7FC] overflow-hidden"
						>
							<img
								class="h-10 drop-shadow-category"
								:src="category.thumb"
								alt="category"
							/>
							<h3
								class="w-full text-xs leading-[16px] whitespace-nowrap overflow-hidden text-ellipsis font-medium font-rubik"
							>
								{{ category.name }}
							</h3>
						</router-link>
					</SwiperSlide>
				</Swiper>
			</div>

			<div
				v-if="categories.length > 0"
				class="flex flex-wrap gap-3 w-full mb-5 veg-navs"
			>
				<button
					:disabled="
						itemProps.property.type !== null &&
						itemProps.property.type === enums.itemTypeEnum.VEG
					"
					@click.prevent="
						itemProps.property.type === enums.itemTypeEnum.NON_VEG
							? itemTypeReset()
							: itemTypeSet(enums.itemTypeEnum.NON_VEG)
					"
					:class="
						itemProps.property.type === enums.itemTypeEnum.NON_VEG
							? 'veg-active'
							: ''
					"
					type="button"
					class="flex items-center gap-3 w-fit pl-3 pr-4 py-1.5 rounded-3xl transition hover:shadow-filter hover:bg-white bg-[#EFF0F6]"
				>
					<img :src="setting.image_vag" alt="category" class="h-6" />
					<span class="capitalize text-sm font-medium text-heading">{{
						$t("label.frontend_non_veg")
					}}</span>
					<i
						class="lab-close-circle-line text-xl text-red-500 transition opacity-0 ltr:-ml-8 rtl:-mr-8 clear-item-type-filter font-fill-danger lab-font-size-24"
					></i>
				</button>
				<button
					:disabled="
						itemProps.property.type !== null &&
						itemProps.property.type === enums.itemTypeEnum.NON_VEG
					"
					@click.prevent="
						itemProps.property.type === enums.itemTypeEnum.VEG
							? itemTypeReset()
							: itemTypeSet(enums.itemTypeEnum.VEG)
					"
					:class="
						itemProps.property.type === enums.itemTypeEnum.VEG
							? 'veg-active'
							: ''
					"
					type="button"
					class="flex items-center gap-3 w-fit pl-3 pr-4 py-1.5 rounded-3xl transition hover:shadow-filter hover:bg-white bg-[#EFF0F6]"
				>
					<img :src="setting.image_non_vag" alt="category" class="h-6" />
					<span class="capitalize text-sm font-medium text-heading">{{
						$t("label.veg")
					}}</span>
					<i
						class="lab-close-circle-line text-xl text-red-500 transition opacity-0 ltr:-ml-8 rtl:-mr-8 font-fill-danger lab-font-size-24"
					></i>
				</button>
			</div>

			<div
				v-if="Object.keys(category).length > 0"
				class="flex gap-4 items-center justify-between mb-6"
			>
				<h2
					class="capitalize text-[26px] leading-[40px] font-semibold text-center sm:text-left text-primary"
				>
					{{ category.name }}
				</h2>
				<div class="flex items-center gap-3">
					<button
						type="button"
						class="lab lab-row-vertical lab-font-size-20 text-xl"
						v-on:click="itemProps.property.design = enums.itemDesignEnum.LIST"
						:class="
							itemProps.property.design === enums.itemDesignEnum.LIST
								? 'text-primary'
								: 'text-[#A0A3BD]'
						"
					></button>
					<button
						type="button"
						class="lab lab-element-3 lab-font-size-20 text-xl"
						v-on:click="itemProps.property.design = enums.itemDesignEnum.GRID"
						:class="
							itemProps.property.design === enums.itemDesignEnum.GRID
								? 'text-primary'
								: 'text-[#A0A3BD]'
						"
					></button>
				</div>
			</div>
			<ItemComponent
				v-if="items.length > 0"
				:items="items"
				:type="itemProps.property.type"
				:design="itemProps.property.design"
			/>
			<div class="mt-12" v-else>
				<div class="max-w-[250px] mx-auto">
					<img
						class="w-full mb-8"
						:src="setting.image_order_not_found"
						alt="image_order_not_found"
					/>
				</div>
				<span class="w-full mb-4 text-center text-black">{{
					$t("message.no_data_available")
				}}</span>
			</div>
		</div>
	</section>

	<div
		v-if="Object.keys(order).length > 0"
		ref="confirmOrder"
		id="confirm-order"
		class="modal confirm-order ff-modal"
	>
		<div class="modal-dialog max-w-[360px] relative">
			<button
				class="modal-close fa-regular fa-circle-xmark absolute top-5 right-5"
				@click.prevent="closeModal"
			></button>
			<div class="modal-body">
				<h3 class="capitalize text-base font-medium text-center mt-2 mb-3">
					{{ $t("message.order_thank_you") }}
				</h3>
				<img
					class="w-[120px] mx-auto mb-3"
					:src="setting.image_confirm"
					alt="gif"
				/>
				<h3
					class="capitalize text-lg font-medium text-center mb-3 text-primary"
				>
					{{ $t("label.order_confirmed") }}
				</h3>
				<p class="text-sm leading-6 mb-4">
					{{ $t("message.order_confirm") }}
					<b class="font-medium">{{ $t("label.dining_table") }}. </b>
					<strong
						class="font-normal"
						v-if="
							setting.site_online_payment_gateway ===
								enums.activityEnum.ENABLE &&
							order.transaction === null &&
							order.payment_status === enums.paymentStatusEnum.UNPAID &&
							paymentMethod === 'digitalPayment'
						"
					>
						{{ $t("message.choosing_payment_options") }}
					</strong>
				</p>

				<div
					class="flex gap-6"
					v-if="
						setting.site_online_payment_gateway === enums.activityEnum.ENABLE &&
						order.transaction === null &&
						order.payment_status === enums.paymentStatusEnum.UNPAID &&
						paymentMethod === 'digitalPayment'
					"
				>
					<router-link
						@click.prevent="closeModal"
						class="w-full rounded-3xl text-center font-medium leading-6 py-3 border border-primary text-primary bg-white"
						:to="{
							name: 'table.tableOrder.details',
							params: { slug: this.$route.params.slug, id: order.id },
						}"
					>
						{{ $t("button.go_to_order") }}
					</router-link>
					<a
						:href="'/payment/' + order.id + '/pay'"
						class="w-full rounded-3xl text-center font-medium leading-6 py-3 text-white bg-primary"
					>
						{{ $t("button.pay_now") }}
					</a>
				</div>

				<router-link
					v-else
					@click.prevent="closeModal"
					class="w-full rounded-3xl text-center font-medium leading-6 py-3 text-white bg-primary"
					:to="{
						name: 'table.tableOrder.details',
						params: { slug: this.$route.params.slug, id: order.id },
					}"
				>
					{{ $t("button.go_to_order") }}
				</router-link>
			</div>
		</div>
	</div>

	<!-- Botón flotante mejorado -->
	<button
		class="fixed bottom-6 right-6 z-50 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-full shadow-2xl px-6 py-4 text-lg font-semibold hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-300"
		@click="showScreensaver = true"
	>
		<i class="fas fa-tv mr-2"></i>
		Salva Pantalla
	</button>

	<!-- Salva pantalla mejorado -->
	<div
		v-if="showScreensaver"
		@click="showScreensaver = false"
		class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-95 cursor-pointer screensaver-overlay"
	>
		<img
			src="/images/theme/pollo.png"
			alt="Screensaver"
			class="w-full h-full object-cover screensaver-bg"
		/>

		<!-- Overlay gradient para mejor legibilidad -->
		<div
			class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black opacity-30"
		></div>

		<!-- Contenedor de mensajes mejorado -->
		<div
			class="absolute bottom-12 right-12 flex flex-col items-end gap-6 z-10 message-container"
		>
			<transition-group
				name="message-slide"
				tag="div"
				class="flex flex-col items-end gap-6"
			>
				<div
					v-for="(message, idx) in visibleMessages"
					:key="`msg-${idx}`"
					class="message-bubble"
					:style="{ animationDelay: idx * 0.3 + 's' }"
				>
					<!-- Mensaje principal -->
					<div class="bubble-content">
						<div class="bubble-text">
							{{ message.text }}
						</div>
						<div class="bubble-time">
							{{ message.time }}
						</div>
						<div class="bubble-tail"></div>

						<!-- Checkmarks de estado -->
						<div class="message-status">
							<svg class="check-mark" viewBox="0 0 24 24" fill="currentColor">
								<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
							</svg>
							<svg
								class="check-mark double"
								viewBox="0 0 24 24"
								fill="currentColor"
							>
								<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
							</svg>
						</div>
					</div>
				</div>
			</transition-group>
		</div>

		<!-- Indicador de "Toca para continuar" -->
		<div
			class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white text-lg font-medium opacity-70 animate-pulse"
		>
			<i class="fas fa-hand-pointer mr-2"></i>
			Toca para continuar
		</div>
	</div>
</template>

<script>
import LoadingComponent from "../../table/components/LoadingComponent.vue";
import statusEnum from "../../../enums/modules/statusEnum";
import ItemComponent from "../components/ItemComponent.vue";
import itemDesignEnum from "../../../enums/modules/itemDesignEnum";
import itemTypeEnum from "../../../enums/modules/itemTypeEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import activityEnum from "../../../enums/modules/activityEnum";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";

export default {
	name: "TableMenuComponent",
	components: {
		ItemComponent,
		LoadingComponent,
		Swiper,
		SwiperSlide,
	},
	data() {
		return {
			loading: {
				isActive: false,
			},

			category: {
				id: 0,
				name: this.$t("label.all") + " " + this.$t("label.items"),
			},
			categoryProps: {
				search: {
					paginate: 0,
					order_column: "sort",
					order_type: "asc",
					status: statusEnum.ACTIVE,
				},
			},
			settings: {
				itemsToShow: 8,
				wrapAround: false,
				snapAlign: "start",
			},
			breakpoints: {
				200: {
					itemsToShow: 1.1,
					wrapAround: false,
					snapAlign: "start",
				},
				250: {
					itemsToShow: 1.5,
					wrapAround: false,
					snapAlign: "start",
				},
				300: {
					itemsToShow: 2.3,
					wrapAround: false,
					snapAlign: "start",
				},
				375: {
					itemsToShow: 2.5,
					wrapAround: false,
					snapAlign: "start",
				},
				540: {
					itemsToShow: 3.5,
					wrapAround: false,
					snapAlign: "start",
				},
				700: {
					itemsToShow: 4.5,
					wrapAround: false,
					snapAlign: "start",
				},
				1024: {
					snapAlign: "start",
					itemsToShow: 7,
					wrapAround: false,
				},
				1180: {
					snapAlign: "start",
					itemsToShow: 8,
					wrapAround: false,
				},
			},
			itemProps: {
				search: {
					paginate: 0,
					order_column: "id",
					order_type: "asc",
					item_category_id: "",
					status: statusEnum.ACTIVE,
				},
				property: {
					design: itemDesignEnum.LIST,
					type: null,
				},
			},
			enums: {
				activityEnum: activityEnum,
				paymentStatusEnum: paymentStatusEnum,
				itemTypeEnum: itemTypeEnum,
				itemDesignEnum: itemDesignEnum,
				orderTypeEnumArray: {
					[orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
					[orderTypeEnum.TAKEAWAY]: this.$t("label.takeaway"),
					[orderTypeEnum.DINING_TABLE]: this.$t("label.dining_table"),
				},
			},
			showScreensaver: false,
			messages: [
				"¡Hola! Bienvenidos a Pollo Pinulito ",
				"Por favor selecciona tus platillos favoritos ",
				"¡Tenemos los mejores sabores para ti! ",
				"¿Listo para ordenar? ¡Estamos aquí para ayudarte! ",
			],
			visibleMessages: [],
			messageIndex: 0,
		};
	},
	watch: {
		showScreensaver(newVal) {
			if (newVal) {
				this.resetMessages();
			}
		},
	},
	computed: {
		categories: function () {
			return this.$store.getters["tableItemCategory/lists"];
		},
		items: function () {
			return this.$store.getters["frontendItem/lists"];
		},
		setting: function () {
			return this.$store.getters["frontendSetting/lists"];
		},
		order: function () {
			return this.$store.getters["tableDiningOrder/show"];
		},
		paymentMethod: function () {
			return this.$store.getters["tableCart/paymentMethod"];
		},
	},
	mounted() {
		this.loading.isActive = true;
		this.itemList();
		this.$store
			.dispatch("tableItemCategory/lists", this.categoryProps.search)
			.then((res) => {
				this.loading.isActive = false;
			})
			.catch((err) => {
				this.loading.isActive = false;
			});

		if (Object.keys(this.$route.query).length > 0) {
			this.loading.isActive = true;
			this.$store
				.dispatch("tableDiningOrder/show", this.$route.query.id)
				.then((res) => {
					const modalTarget = this.$refs.confirmOrder;
					modalTarget?.classList?.add("active");
					document.body.style.overflowY = "hidden";
					this.loading.isActive = false;
				})
				.catch((err) => {
					this.loading.isActive = false;
				});
		}
	},
	methods: {
		closeModal: function () {
			const modalTarget = this.$refs.confirmOrder;
			modalTarget?.classList?.remove("active");
			document.body.style.overflowY = "auto";
			this.loading.isActive = false;
		},
		resetMessages() {
			this.visibleMessages = [];
			this.messageIndex = 0;
			this.animateMessages();
		},
		animateMessages() {
			if (this.messageIndex < this.messages.length) {
				// Agregar mensaje directamente sin indicador de typing
				this.visibleMessages.push({
					text: this.messages[this.messageIndex],
					typing: false,
					time: this.getCurrentTime(),
				});
				this.messageIndex++;

				// Programar el siguiente mensaje
				setTimeout(() => {
					this.animateMessages();
				}, 2500);
			} else {
				// Reiniciar el ciclo después de todos los mensajes
				setTimeout(() => {
					this.resetMessages();
				}, 6000);
			}
		},
		getCurrentTime() {
			const now = new Date();
			return now.toLocaleTimeString("es-ES", {
				hour: "2-digit",
				minute: "2-digit",
			});
		},
		allCategory: function (category) {
			this.itemProps.search.item_category_id = "";
			this.category = {
				id: 0,
				name: category.name,
			};
			this.itemList();
		},
		setCategory: function (id, slug = null) {
			this.itemProps.search.item_category_id = id;
			this.itemList();
			if (slug !== null) {
				this.loading.isActive = true;
				this.$store
					.dispatch("tableItemCategory/show", {
						slug: slug,
					})
					.then((res) => {
						this.category = res.data.data;
						this.loading.isActive = false;
					})
					.catch((err) => {
						this.loading.isActive = false;
					});
			}
		},
		itemList: function () {
			this.loading.isActive = true;
			this.$store
				.dispatch("frontendItem/lists", this.itemProps.search)
				.then((res) => {
					this.loading.isActive = false;
				})
				.catch((err) => {
					this.loading.isActive = false;
				});
		},
		itemTypeSet: function (e) {
			this.itemProps.property.type = e;
		},
		itemTypeReset: function () {
			this.itemProps.property.type = null;
		},
	},
};
</script>

<style scoped>
/* Screensaver overlay animations */
.screensaver-overlay {
	animation: fadeInOverlay 0.8s ease-in-out;
}

.screensaver-bg {
	animation: zoomInImage 1.2s ease-out;
}

@keyframes fadeInOverlay {
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}

@keyframes zoomInImage {
	from {
		transform: scale(1.1);
		opacity: 0.8;
	}
	to {
		transform: scale(1);
		opacity: 1;
	}
}

/* Container de mensajes */
.message-container {
	max-width: 500px;
	min-width: 380px;
}

/* Burbujas de mensaje mejoradas */
.message-bubble {
	animation: slideInBubble 1s ease-out both;
	transform-origin: bottom right;
}

.bubble-content {
	position: relative;
	background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
	border-radius: 20px 20px 8px 20px;
	padding: 20px 24px 12px 20px;
	margin-bottom: 4px;
	box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
	backdrop-filter: blur(10px);
	border: 1px solid rgba(255, 255, 255, 0.3);
	max-width: 450px;
	min-width: 280px;
	position: relative;
}

.bubble-text {
	color: #1f2937;
	font-size: 22px;
	font-weight: 600;
	line-height: 1.4;
	margin-bottom: 10px;
	word-wrap: break-word;
	font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
}

.bubble-time {
	color: #718096;
	font-size: 12px;
	text-align: right;
	margin-top: 4px;
	display: flex;
	align-items: center;
	justify-content: flex-end;
	gap: 4px;
}

.bubble-tail {
	position: absolute;
	bottom: -2px;
	right: 8px;
	width: 0;
	height: 0;
	border-left: 12px solid transparent;
	border-right: 0;
	border-top: 12px solid #e5e7eb;
	filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.message-status {
	display: inline-flex;
	align-items: center;
	gap: 2px;
}

.check-mark {
	width: 14px;
	height: 14px;
	color: #4299e1;
	animation: checkAppear 0.3s ease-out 0.5s both;
}

.check-mark.double {
	margin-left: -6px;
	color: #48bb78;
	animation-delay: 0.8s;
}

/* Indicador de typing */
.typing-indicator {
	display: flex;
	justify-content: flex-end;
	animation: fadeInTyping 0.5s ease-out;
}

.typing-dots {
	background: rgba(255, 255, 255, 0.9);
	border-radius: 18px;
	padding: 12px 16px;
	display: flex;
	gap: 4px;
	align-items: center;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
	backdrop-filter: blur(10px);
}

.typing-dots span {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: #a0aec0;
	animation: typingDot 1.4s ease-in-out infinite;
}

.typing-dots span:nth-child(2) {
	animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
	animation-delay: 0.4s;
}

/* Animaciones */
@keyframes slideInBubble {
	0% {
		opacity: 0;
		transform: translateY(30px) translateX(20px) scale(0.8);
		filter: blur(4px);
	}
	50% {
		opacity: 0.8;
		filter: blur(2px);
	}
	100% {
		opacity: 1;
		transform: translateY(0) translateX(0) scale(1);
		filter: blur(0);
	}
}

@keyframes checkAppear {
	0% {
		opacity: 0;
		transform: scale(0.5);
	}
	100% {
		opacity: 1;
		transform: scale(1);
	}
}

@keyframes typingDot {
	0%,
	60%,
	100% {
		transform: translateY(0);
		opacity: 0.4;
	}
	30% {
		transform: translateY(-10px);
		opacity: 1;
	}
}

@keyframes fadeInTyping {
	from {
		opacity: 0;
		transform: translateY(10px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

/* Transiciones de grupo */
.message-slide-enter-active {
	transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.message-slide-leave-active {
	transition: all 0.5s ease-in;
}

.message-slide-enter-from {
	opacity: 0;
	transform: translateY(40px) translateX(30px) scale(0.7);
}

.message-slide-leave-to {
	opacity: 0;
	transform: translateY(-20px) scale(0.9);
}

.message-slide-move {
	transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Responsive */
@media (max-width: 768px) {
	.message-container {
		max-width: 420px;
		min-width: 340px;
		bottom: 80px;
		right: 20px;
	}

	.bubble-content {
		max-width: 400px;
		min-width: 260px;
		padding: 18px 22px 10px 18px;
	}

	.bubble-text {
		font-size: 20px;
	}
}

@media (max-width: 480px) {
	.message-container {
		max-width: 360px;
		min-width: 300px;
		bottom: 60px;
		right: 16px;
	}

	.bubble-content {
		max-width: 340px;
		min-width: 220px;
	}

	.bubble-text {
		font-size: 18px;
	}
}
</style>
