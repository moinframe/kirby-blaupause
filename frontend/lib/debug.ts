import getURLParameter from "../composables/getURLParameter"
import DebugService from "../services/DebugService"

export const install = () => {
	if (getURLParameter("debug") === "1") {
		DebugService.enableDebug()
	}
}
