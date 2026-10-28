import DebugService from "../services/DebugService"
import getURLParameter from "../utils/getURLParameter"

export const install = () => {
	if (getURLParameter("debug") === "1") {
		DebugService.enableDebug()
	}
}
